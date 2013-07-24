<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jFWBase::load('jFWTableNested', 'tables._nested');

class nec_Categories extends jFWTableNested {

    /**
     * @param database A database connector object
     */
    function nec_Categories(&$db) {
        $tbl_key = 'category_id';
        $tbl_suffix = 'categories';
        $this->set('_suffix', $tbl_suffix);
        $name = jFWBase::getTablePrefix();

        parent::__construct("#__{$name}{$tbl_suffix}", $tbl_key, $db);
    }

    // overloaded check function
    function check() {
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        $languages = jFWBase::getClass('HelperLanguages', 'helpers.language', $options)->getAllLanguages();

        foreach ($languages as $lang) {
            $name = 'name_' . $lang->language;
            $alias = 'alias_' . $lang->language;

            if ((isset($this->$name)) && (trim($this->$name) == '')) {
                $this->_error = JText::_('COM_JNEGOCIO_ADD_NAME');
                JError::raiseWarning('SOME_ERROR_CODE', $this->_error);
                return false;
            } else {
                $this->$name = trim($this->$name);
            }

            $tempalias = JFilterOutput::stringURLSafe($this->$name);
            if (empty($this->$alias) || $this->$alias === $tempalias) {
                $this->$alias = $tempalias;
            }
        }

        return parent::check();
    }

    /**
     * Attempts base getRoot() and if it fails, creates root entry
     * 
     * @see tienda/admin/tables/_baseNested#getRootId()
     */
    function getRootId() {
        if (!$result = parent::getRootId()) {
            $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
            $languages = jFWBase::getClass('HelperLanguages', 'helpers.language', $options)->getAllLanguages();

            // add root
            $db = $this->getDBO();
            $query = "INSERT IGNORE INTO `" . $this->getTableName() . "` SET ";
            foreach ($languages as $lang) {
                $query .= "`name_" . $lang->language . "` = 'All Categories',";
                $query .= "`alias_" . $lang->language . "` = 'root_" . $lang->lang . "',";
            }
            $query .= "`parent_id` = '0', `lft` = '0', `rgt` = '1', `published` = '1', `isroot` = '1'; ";

            $db->setQuery($query);
            if ($db->query()) {
                $insertid = $db->insertid();
                $this->load($insertid);
                $this->rebuild();
                $result = $insertid;
            } else {
                $this->setError($db->getErrorMsg());
                return false;
            }
        }

        return $result;
    }

    /**
     * (non-PHPdoc)
     */
    function getTreeList($parent = null, $enabled = '1', $indent = ' ') {
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        jFWBase::load('HelperLanguages', 'helpers.languages', $options);
        $lang = &HelperLanguages::getlang();

        $key = $this->getKeyName();
        $pk = (is_null($parent)) ? $this->$key : $parent;
        $db = $this->getDBO();

        if (intval($enabled) > 0) {
            $enabled_query = "AND node.published = '1'"
                    . " AND NOT EXISTS ( SELECT * FROM " . $this->getTableName() . " AS tbl"
                    . " WHERE tbl.lft < node.lft AND tbl.rgt > node.rgt"
                    . " AND tbl.published = '0'"
                    . " ORDER BY tbl.lft ASC )";
        }

        $query = "SELECT node.*, COUNT(parent." . $key . ") AS level"
                . " FROM " . $this->getTableName() . " AS node,"
                . $this->getTableName() . " AS parent"
                . " WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.isroot = 0 "
                . $enabled_query
                . " GROUP BY node." . $key
                . " ORDER BY node.lft"
        ;
//			. ", CONCAT( REPEAT('".$indent."', COUNT(`parent.".$lang->getField('name')."`) - 1), `node.".$lang->getField('name')."`) AS name"			
//		echo $query."<br/>";
        $db->setQuery($query);
        $return = $db->loadObjectList();
        return $return;
    }

    /** 	
     * Method to recursively rebuild the whole nested set tree.
     *
     * @param   integer  $parentId  The root of the tree to rebuild.
     * @param   integer  $leftId    The left id to start with in building the tree.
     * @param   integer  $level     The level to assign to the current nodes.
     * @param   string   $path      The path to the current nodes.
     *
     * @return  integer  1 + value of root rgt on success, false on failure
     *
     * @link    http://docs.joomla.org/JTableNested/rebuild
     * @since   11.1
     */
    public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '') {
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        jFWBase::load('HelperLanguages', 'helpers.languages', $options);
        $lang = &HelperLanguages::getlang();
        $db = $this->getDBO();

        // If no parent is provided, try to find it.
        if ($parentId === null) {
            // Get the root item.
            $parentId = $this->getRootId();
            if ($parentId === false) {
                return false;
            }
        }

        // Build the structure of the recursive query.
        if (!isset($this->_cache['rebuild.sql'])) {
            $query = $db->getQuery(true);
            $query->select($this->_tbl_key . ', `' . $lang->getField('alias') . '`');
            $query->from($this->getTableName());
            $query->where('parent_id = %d');

            // If the table has an ordering field, use that for ordering.
            if (property_exists($this, 'ordering')) {
                $query->order('parent_id, ordering, lft');
            } else {
                $query->order('parent_id, lft');
            }
            // echo "query:".(string) $query;
            $this->_cache['rebuild.sql'] = (string) $query;
        }

        // Make a shortcut to database object.
        // Assemble the query to find all children of this node.
        $db->setQuery(sprintf($this->_cache['rebuild.sql'], (int) $parentId));
        $children = $db->loadObjectList();

        // The right value of this node is the left value + 1
        $rightId = $leftId + 1;

        // execute this function recursively over all children
        foreach ($children as $node) {
            // $rightId is the current right value, which is incremented on recursion return.
            // Increment the level for the children.
            // Add this item's alias to the path (but avoid a leading /)
            $rightId = $this->rebuild($node->{$this->_tbl_key}, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);

            // If there is an update failure, return false to break out of the recursion.
            if ($rightId === false) {
                return false;
            }
        }

        // We've got the left value, and now that we've processed
        // the children of this node we also know the right value.
        $query = $db->getQuery(true);
        $query->update($this->getTableName());
        $query->set('lft = ' . (int) $leftId);
        $query->set('rgt = ' . (int) $rightId);
        $query->set('level = ' . (int) $level);
        $query->set('path = ' . $db->quote($path));
        $query->where($this->_tbl_key . ' = ' . (int) $parentId);
        $db->setQuery($query);

        // If there is an update failure, return false to break out of the recursion.
        if (!$db->query()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Return the right value of this node + 1.
        return $rightId + 1;
    }

    /**
     * Method to rebuild the node's path field from the alias values of the
     * nodes from the current node to the root node of the tree.
     *
     * @param   integer  $pk  Primary key of the node for which to get the path.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/rebuildPath
     * @since   11.1
     */
    public function rebuildPath($pk = null) {
        $options = array('site' => 'admin', 'type' => 'components', 'ext' => 'com_jnegocio');
        jFWBase::load('HelperLanguages', 'helpers.languages', $options);
        $lang = &HelperLanguages::getlang();
        $db = $this->getDBO();

        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Get the aliases for the path from the node to the root node.
        $query = $db->getQuery(true);
        $query->select('p.`' . $lang->getField('alias') . '`');
        $query->from($this->getTableName() . ' AS n, ' . $this->getTableName() . ' AS p');
        $query->where('n.lft BETWEEN p.lft AND p.rgt');
        $query->where('n.' . $this->_tbl_key . ' = ' . (int) $pk);
        $query->order('p.lft');
        $db->setQuery($query);

        $segments = $db->loadColumn();

        // Make sure to remove the root path if it exists in the list.
        if ($segments[0] == 'root') {
            array_shift($segments);
        }

        // Build the path.
        $path = trim(implode('/', $segments), ' /\\');

        // Update the path field for the node.
        $query = $db->getQuery(true);
        $query->update($this->getTableName());
        $query->set('path = ' . $db->quote($path));
        $query->where($this->_tbl_key . ' = ' . (int) $pk);
        $db->setQuery($query);

        // Check for a database error.
        if (!$db->query()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILDPATH_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Update the current record's path to the new one:
        $this->path = $path;

        return true;
    }

}