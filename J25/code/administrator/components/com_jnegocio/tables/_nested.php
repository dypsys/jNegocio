<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2013 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jFWBase::load('jFWTable', 'tables._base');

/**
 * Abstract Table class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	FrameWork
 * @subpackage	Admin
 * @since		2.5
 */
class jFWTableNested extends jFWTable {

    /**
     * Object property holding the primary key of the parent node.  Provides
     * adjacency list data for nodes.
     *
     * @var    integer
     * @since  11.1
     */
    public $parent_id;

    /**
     * Object property holding the depth level of the node in the tree.
     *
     * @var    integer
     * @since  11.1
     */
    public $level;

    /**
     * Object property holding the left value of the node for managing its
     * placement in the nested sets tree.
     *
     * @var    integer
     * @since  11.1
     */
    public $lft;

    /**
     * Object property holding the right value of the node for managing its
     * placement in the nested sets tree.
     *
     * @var    integer
     * @since  11.1
     */
    public $rgt;

    /**
     * Object property holding the alias of this node used to constuct the
     * full text path, forward-slash delimited.
     *
     * @var    string
     * @since  11.1
     */
    public $alias;

    /**
     * Object property to hold the location type to use when storing the row.
     * Possible values are: ['before', 'after', 'first-child', 'last-child'].
     *
     * @var    string
     * @since  11.1
     */
    protected $_location;

    /**
     * Object property to hold the primary key of the location reference node to
     * use when storing the row.  A combination of location type and reference
     * node describes where to store the current node in the tree.
     *
     * @var    integer
     * @since  11.1
     */
    protected $_location_id;

    /**
     * An array to cache values in recursive processes.
     *
     * @var    array
     * @since  11.1
     */
    protected $_cache = array();

    /**
     * Debug level
     *
     * @var    integer
     * @since  11.1
     */
    protected $_debug = 0;

    /**
     * Sets the debug level on or off
     *
     * @param   integer  $level  0 = off, 1 = on
     *
     * @return  void
     *
     * @since   11.1
     */
    public function debug($level) {
        $this->_debug = intval($level);
    }

    /**
     * Method to get an array of nodes from a given node to its root.
     *
     * @param   integer  $pk          Primary key of the node for which to get the path.
     * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
     *
     * @return  mixed    Boolean false on failure or array of node objects on success.
     *
     * @link    http://docs.joomla.org/JTableNested/getPath
     * @since   11.1
     */
    public function getPath($pk = null, $diagnostic = false) {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
        $db = $this->getDBO();

        // Get the path from the node to the root.
        $query = $db->getQuery(true);
        $select = ($diagnostic) ? 'p.' . $k . ', p.parent_id, p.level, p.lft, p.rgt' : 'p.*';
        $query->select($select);
        $query->from($this->_tbl . ' AS n, ' . $this->getTableName() . ' AS p');
        $query->where('n.lft BETWEEN p.lft AND p.rgt');
        $query->where('n.' . $k . ' = ' . (int) $pk);
        $query->order('p.lft');

        $db->setQuery($query);
        $path = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GET_PATH_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        return $path;
    }

    /**
     * Method to get a node and all its child nodes.
     *
     * @param   integer  $pk          Primary key of the node for which to get the tree.
     * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
     *
     * @return  mixed    Boolean false on failure or array of node objects on success.
     *
     * @link    http://docs.joomla.org/JTableNested/getTree
     * @since   11.1
     */
    public function getTree($pk = null, $diagnostic = false) {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
        $db = $this->getDBO();

        // Get the node and children as a tree.
        $query = $db->getQuery(true);
        $select = ($diagnostic) ? 'n.' . $k . ', n.parent_id, n.level, n.lft, n.rgt' : 'n.*';
        $query->select($select);
        $query->from($this->_tbl . ' AS n, ' . $this->getTableName() . ' AS p');
        $query->where('n.lft BETWEEN p.lft AND p.rgt');
        $query->where('p.' . $k . ' = ' . (int) $pk);
        $query->order('n.lft');
        $db->setQuery($query);
        $tree = $db->loadObjectList();

        // Check for a database error.
        if ($db->getErrorNum()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GET_TREE_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        return $tree;
    }

    /**
     * Method to set the location of a node in the tree object.  This method does not
     * save the new location to the database, but will set it in the object so
     * that when the node is stored it will be stored in the new location.
     *
     * @param   integer  $referenceId  The primary key of the node to reference new location by.
     * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/setLocation
     * @since   11.1
     */
    public function setLocation($referenceId, $position = 'after') {
        // Make sure the location is valid.
        if (($position != 'before') && ($position != 'after') && ($position != 'first-child') && ($position != 'last-child')) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_LOCATION', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Set the location properties.
        $this->_location = $position;
        $this->_location_id = $referenceId;

        return true;
    }

    /**
     * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
     * Negative numbers move the row up in the sequence and positive numbers move it down.
     *
     * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
     * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the
     * ordering values.
     *
     * @return  mixed    Boolean true on success.
     *
     * @link    http://docs.joomla.org/JTable/move
     * @since   11.1
     */
    public function move($delta, $where = '') {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = $this->$k;
        $db = $this->getDBO();

        $query = $db->getQuery(true);
        $query->select($k);
        $query->from($this->getTableName());
        $query->where('parent_id = ' . $this->parent_id);
        if ($where) {
            $query->where($where);
        }

        $position = 'after';
        if ($delta > 0) {
            $query->where('rgt > ' . $this->rgt);
            $query->order('rgt ASC');
            $position = 'after';
        } else {
            $query->where('lft < ' . $this->lft);
            $query->order('lft DESC');
            $position = 'before';
        }

        $db->setQuery($query);
        $referenceId = $db->loadResult();

        if ($referenceId) {
            return $this->moveByReference($referenceId, $position, $pk);
        } else {
            return false;
        }
    }

    /**
     * Method to move a node and its children to a new location in the tree.
     *
     * @param   integer  $referenceId  The primary key of the node to reference new location by.
     * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
     * @param   integer  $pk           The primary key of the node to move.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/moveByReference
     * @since   11.1
     */
    public function moveByReference($referenceId, $position = 'after', $pk = null) {
        if ($this->_debug) {
            echo "\nMoving ReferenceId:$referenceId, Position:$position, PK:$pk";
        }

        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
        $db = $this->getDBO();

        // Get the node by id.
        if (!$node = $this->_getNode($pk)) {
            // Error message set in getNode method.
            return false;
        }

        // Get the ids of child nodes.
        $query = $db->getQuery(true);
        $query->select($k);
        $query->from($this->getTableName());
        $query->where('lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);
        $db->setQuery($query);
        $children = $db->loadColumn();

        // Check for a database error.
        if ($db->getErrorNum()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_MOVE_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }
        if ($this->_debug) {
            $this->_logtable(false);
        }

        // Cannot move the node to be a child of itself.
        if (in_array($referenceId, $children)) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_NODE_RECURSION', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Lock the table for writing.
        if (!$this->_lock()) {
            return false;
        }

        /*
         * Move the sub-tree out of the nested sets by negating its left and right values.
         */
        $query = $db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft * (-1), rgt = rgt * (-1)');
        $query->where('lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);
        $db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        /*
         * Close the hole in the tree that was opened by removing the sub-tree from the nested sets.
         */
        // Compress the left values.
        $query = $db->getQuery(true);
        $query->update($this->getTableName());
        $query->set('lft = lft - ' . (int) $node->width);
        $query->where('lft > ' . (int) $node->rgt);
        $db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // Compress the right values.
        $query = $db->getQuery(true);
        $query->update($this->getTableName());
        $query->set('rgt = rgt - ' . (int) $node->width);
        $query->where('rgt > ' . (int) $node->rgt);
        $db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // We are moving the tree relative to a reference node.
        if ($referenceId) {
            // Get the reference node by primary key.
            if (!$reference = $this->_getNode($referenceId)) {
                // Error message set in getNode method.
                $this->_unlock();
                return false;
            }

            // Get the reposition data for shifting the tree and re-inserting the node.
            if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, $position)) {
                // Error message set in getNode method.
                $this->_unlock();
                return false;
            }
        } else {
            // We are moving the tree to be the last child of the root node
            // Get the last root node as the reference node.
            $query = $db->getQuery(true);
            $query->select($this->_tbl_key . ', parent_id, level, lft, rgt');
            $query->from($this->getTableName());
            $query->where('parent_id = 0');
            $query->order('lft DESC');
            $db->setQuery($query, 0, 1);
            $reference = $db->loadObject();

            // Check for a database error.
            if ($db->getErrorNum()) {
                $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_MOVE_FAILED', get_class($this), $db->getErrorMsg()));
                $this->setError($e);
                $this->_unlock();
                return false;
            }

            if ($this->_debug) {
                $this->_logtable(false);
            }

            // Get the reposition data for re-inserting the node after the found root.
            if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, 'last-child')) {
                // Error message set in getNode method.
                $this->_unlock();
                return false;
            }
        }

        /*
         * Create space in the nested sets at the new location for the moved sub-tree.
         */
        // Shift left values.
        $query = $db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('lft = lft + ' . (int) $node->width);
        $query->where($repositionData->left_where);
        $db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // Shift right values.
        $query = $db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('rgt = rgt + ' . (int) $node->width);
        $query->where($repositionData->right_where);
        $db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        /*
         * Calculate the offset between where the node used to be in the tree and
         * where it needs to be in the tree for left ids (also works for right ids).
         */
        $offset = $repositionData->new_lft - $node->lft;
        $levelOffset = $repositionData->new_level - $node->level;

        // Move the nodes back into position in the tree using the calculated offsets.
        $query = $db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('rgt = ' . (int) $offset . ' - rgt');
        $query->set('lft = ' . (int) $offset . ' - lft');
        $query->set('level = level + ' . (int) $levelOffset);
        $query->where('lft < 0');
        $db->setQuery($query);

        $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');

        // Set the correct parent id for the moved node if required.
        if ($node->parent_id != $repositionData->new_parent_id) {
            $query = $db->getQuery(true);
            $query->update($this->_tbl);

            // Update the title and alias fields if they exist for the table.
            if (property_exists($this, 'title') && $this->title !== null) {
                $query->set('title = ' . $db->Quote($this->title));
            }
            if (property_exists($this, 'alias') && $this->alias !== null) {
                $query->set('alias = ' . $db->Quote($this->alias));
            }

            $query->set('parent_id = ' . (int) $repositionData->new_parent_id);
            $query->where($this->_tbl_key . ' = ' . (int) $node->$k);
            $db->setQuery($query);

            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_MOVE_FAILED');
        }

        // Unlock the table for writing.
        $this->_unlock();

        // Set the object values.
        $this->parent_id = $repositionData->new_parent_id;
        $this->level = $repositionData->new_level;
        $this->lft = $repositionData->new_lft;
        $this->rgt = $repositionData->new_rgt;

        return true;
    }

    /**
     * Method to delete a node and, optionally, its child nodes from the table.
     *
     * @param   integer  $pk        The primary key of the node to delete.
     * @param   boolean  $children  True to delete child nodes, false to move them up a level.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/delete
     * @since   11.1
     */
    public function delete($pk = null, $children = true) {
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
        $db = $this->getDBO();

        // Lock the table for writing.
        if (!$this->_lock()) {
            // Error message set in lock method.
            return false;
        }

        // Get the node by id.
        if (!$node = $this->_getNode($pk)) {
            // Error message set in getNode method.
            $this->_unlock();
            return false;
        }

        // Should we delete all children along with the node?
        if ($children) {
            // Delete the node and all of its children.
            $query = $db->getQuery(true);
            $query->delete();
            $query->from($this->getTableName());
            $query->where('lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Compress the left values.
            $query = $db->getQuery(true);
            $query->update($this->getTableName());
            $query->set('lft = lft - ' . (int) $node->width);
            $query->where('lft > ' . (int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Compress the right values.
            $query = $db->getQuery(true);
            $query->update($this->getTableName());
            $query->set('rgt = rgt - ' . (int) $node->width);
            $query->where('rgt > ' . (int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
        } else {
            // Leave the children and move them up a level.
            // Delete the node.
            $query = $db->getQuery(true);
            $query->delete();
            $query->from($this->getTableName());
            $query->where('lft = ' . (int) $node->lft);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Shift all node's children up a level.
            $query = $db->getQuery(true);
            $query->update($this->getTableName());
            $query->set('lft = lft - 1');
            $query->set('rgt = rgt - 1');
            $query->set('level = level - 1');
            $query->where('lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Adjust all the parent values for direct children of the deleted node.
            $query = $db->getQuery(true);
            $query->update($this->getTableName());
            $query->set('parent_id = ' . (int) $node->parent_id);
            $query->where('parent_id = ' . (int) $node->$k);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Shift all of the left values that are right of the node.
            $query = $db->getQuery(true);
            $query->update($this->getTableName());
            $query->set('lft = lft - 2');
            $query->where('lft > ' . (int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');

            // Shift all of the right values that are right of the node.
            $query = $db->getQuery(true);
            $query->update($this->getTableName());
            $query->set('rgt = rgt - 2');
            $query->where('rgt > ' . (int) $node->rgt);
            $this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
        }

        // Unlock the table for writing.
        $this->_unlock();

        return true;
    }

    /**
     * Asset that the nested set data is valid.
     *
     * @return  boolean  True if the instance is sane and able to be stored in the database.
     *
     * @link    http://docs.joomla.org/JTable/check
     * @since   11.1
     */
    public function check() {
        $db = $this->getDBO();
        $this->parent_id = (int) $this->parent_id;
        if ($this->parent_id > 0) {
            $query = $db->getQuery(true);
            $query->select('COUNT(' . $this->_tbl_key . ')');
            $query->from($this->getTableName());
            $query->where($this->_tbl_key . ' = ' . $this->parent_id);
            $db->setQuery($query);

            if ($db->loadResult()) {
                return true;
            } else {
                if ($db->getErrorNum()) {
                    $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_CHECK_FAILED', get_class($this), $db->getErrorMsg()));
                    $this->setError($e);
                } else {
                    $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_PARENT_ID', get_class($this)));
                    $this->setError($e);
                }
            }
        } else {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_INVALID_PARENT_ID', get_class($this)));
            $this->setError($e);
        }

        return false;
    }

    /**
     * Method to store a node in the database table.
     *
     * @param   boolean  $updateNulls  True to update null values as well.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/store
     * @since   11.1
     */
    public function store($updateNulls = false) {
        // Initialise variables.
        $k = $this->_tbl_key;
        $db = $this->getDBO();

        if ($this->_debug) {
            echo "\n" . get_class($this) . "::store\n";
            $this->_logtable(true, false);
        }
        /*
         * If the primary key is empty, then we assume we are inserting a new node into the
         * tree.  From this point we would need to determine where in the tree to insert it.
         */
        if (empty($this->$k)) {
            /*
             * We are inserting a node somewhere in the tree with a known reference
             * node.  We have to make room for the new node and set the left and right
             * values before we insert the row.
             */
            if ($this->_location_id >= 0) {
                // Lock the table for writing.
                if (!$this->_lock()) {
                    // Error message set in lock method.
                    return false;
                }

                // We are inserting a node relative to the last root node.
                if ($this->_location_id == 0) {
                    // Get the last root node as the reference node.
                    $query = $db->getQuery(true);
                    $query->select($this->_tbl_key . ', parent_id, level, lft, rgt');
                    $query->from($this->_tbl);
                    $query->where('parent_id = 0');
                    $query->order('lft DESC');
                    $db->setQuery($query, 0, 1);
                    $reference = $db->loadObject();

                    // Check for a database error.
                    if ($db->getErrorNum()) {
                        $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED', get_class($this), $db->getErrorMsg()));
                        $this->setError($e);
                        $this->_unlock();
                        return false;
                    }

                    if ($this->_debug) {
                        $this->_logtable(false);
                    }
                } else {
                    // We have a real node set as a location reference.
                    // Get the reference node by primary key.
                    if (!$reference = $this->_getNode($this->_location_id)) {
                        // Error message set in getNode method.
                        $this->_unlock();
                        return false;
                    }
                }

                // Get the reposition data for shifting the tree and re-inserting the node.
                if (!($repositionData = $this->_getTreeRepositionData($reference, 2, $this->_location))) {
                    // Error message set in getNode method.
                    $this->_unlock();
                    return false;
                }

                // Create space in the tree at the new location for the new node in left ids.
                $query = $db->getQuery(true);
                $query->update($this->getTableName());
                $query->set('lft = lft + 2');
                $query->where($repositionData->left_where);
                $this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

                // Create space in the tree at the new location for the new node in right ids.
                $query = $db->getQuery(true);
                $query->update($this->getTableName());
                $query->set('rgt = rgt + 2');
                $query->where($repositionData->right_where);
                $this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

                // Set the object values.
                $this->parent_id = $repositionData->new_parent_id;
                $this->level = $repositionData->new_level;
                $this->lft = $repositionData->new_lft;
                $this->rgt = $repositionData->new_rgt;
            } else {
                // Negative parent ids are invalid
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_INVALID_PARENT_ID'));
                $this->setError($e);
                return false;
            }
        } else {
            /*
             * If we have a given primary key then we assume we are simply updating this
             * node in the tree.  We should assess whether or not we are moving the node
             * or just updating its data fields.
             */
            // If the location has been set, move the node to its new location.
            if ($this->_location_id > 0) {
                if (!$this->moveByReference($this->_location_id, $this->_location, $this->$k)) {
                    // Error message set in move method.
                    return false;
                }
            }

            // Lock the table for writing.
            if (!$this->_lock()) {
                // Error message set in lock method.
                return false;
            }
        }

        // Store the row to the database.
        if (!parent::store($updateNulls)) {
            $this->_unlock();
            return false;
        }

        if ($this->_debug) {
            $this->_logtable();
        }

        // Unlock the table for writing.
        $this->_unlock();

        return true;
    }

    /**
     * Gets the ID of the root item in the tree
     *
     * @return  mixed  The ID of the root row, or false and the internal error is set.
     *
     * @since   11.1
     */
    public function getRootId() {
        // Get the root item.
        $k = $this->_tbl_key;
        $db = $this->getDBO();

        // Test for a unique record with parent_id = 0
        $query = $db->getQuery(true);
        $query->select($k);
        $query->from($this->getTableName());
        $query->where('parent_id = 0');
        $db->setQuery($query);

        $result = $db->loadColumn();

        if ($db->getErrorNum()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        if (count($result) == 1) {
            $parentId = $result[0];
        } else {
            // Test for a unique record with lft = 0
            $query = $db->getQuery(true);
            $query->select($k);
            $query->from($this->getTableName());
            $query->where('lft = 0');
            $db->setQuery($query);

            $result = $db->loadColumn();
            if ($db->getErrorNum()) {
                $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $db->getErrorMsg()));
                $this->setError($e);
                return false;
            }

            if (count($result) == 1) {
                $parentId = $result[0];
            } elseif (property_exists($this, 'alias')) {
                // Test for a unique record alias = root
                $query = $db->getQuery(true);
                $query->select($k);
                $query->from($this->getTableName());
                $query->where('alias = ' . $db->quote('root'));
                $db->setQuery($query);

                $result = $db->loadColumn();
                if ($db->getErrorNum()) {
                    $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETROOTID_FAILED', get_class($this), $db->getErrorMsg()));
                    $this->setError($e);
                    return false;
                }

                if (count($result) == 1) {
                    $parentId = $result[0];
                } else {
                    $e = new JException(JText::_('JLIB_DATABASE_ERROR_ROOT_NODE_NOT_FOUND'));
                    $this->setError($e);
                    return false;
                }
            } else {
                $e = new JException(JText::_('JLIB_DATABASE_ERROR_ROOT_NODE_NOT_FOUND'));
                $this->setError($e);
                return false;
            }
        }

        return $parentId;
    }

    /**
     * Method to get nested set properties for a node in the tree.
     *
     * @param   integer  $id   Value to look up the node by.
     * @param   string   $key  Key to look up the node by.
     *
     * @return  mixed    Boolean false on failure or node object on success.
     *
     * @since   11.1
     */
    protected function _getNode($id, $key = null) {
        $db = $this->getDBO();

        // Determine which key to get the node base on.
        switch ($key) {
            case 'parent':
                $k = 'parent_id';
                break;
            case 'left':
                $k = 'lft';
                break;
            case 'right':
                $k = 'rgt';
                break;
            default:
                $k = $this->_tbl_key;
                break;
        }

        // Get the node data.
        $query = $db->getQuery(true);
        $query->select($this->_tbl_key . ', parent_id, level, lft, rgt');
        $query->from($this->getTableName());
        $query->where($k . ' = ' . (int) $id);
        $db->setQuery($query, 0, 1);

        $row = $db->loadObject();

        // Check for a database error or no $row returned
        if ((!$row) || ($db->getErrorNum())) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_GETNODE_FAILED', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Do some simple calculations.
        $row->numChildren = (int) ($row->rgt - $row->lft - 1) / 2;
        $row->width = (int) $row->rgt - $row->lft + 1;

        return $row;
    }

    /**
     * Method to get various data necessary to make room in the tree at a location
     * for a node and its children.  The returned data object includes conditions
     * for SQL WHERE clauses for updating left and right id values to make room for
     * the node as well as the new left and right ids for the node.
     *
     * @param   object   $referenceNode  A node object with at least a 'lft' and 'rgt' with
     * which to make room in the tree around for a new node.
     * @param   integer  $nodeWidth      The width of the node for which to make room in the tree.
     * @param   string   $position       The position relative to the reference node where the room
     * should be made.
     *
     * @return  mixed    Boolean false on failure or data object on success.
     *
     * @since   11.1
     */
    protected function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before') {
        // Make sure the reference an object with a left and right id.
        if (!is_object($referenceNode) && isset($referenceNode->lft) && isset($referenceNode->rgt)) {
            return false;
        }

        // A valid node cannot have a width less than 2.
        if ($nodeWidth < 2) {
            return false;
        }

        // Initialise variables.
        $k = $this->_tbl_key;
        $data = new stdClass;

        // Run the calculations and build the data object by reference position.
        switch ($position) {
            case 'first-child':
                $data->left_where = 'lft > ' . $referenceNode->lft;
                $data->right_where = 'rgt >= ' . $referenceNode->lft;

                $data->new_lft = $referenceNode->lft + 1;
                $data->new_rgt = $referenceNode->lft + $nodeWidth;
                $data->new_parent_id = $referenceNode->$k;
                $data->new_level = $referenceNode->level + 1;
                break;

            case 'last-child':
                $data->left_where = 'lft > ' . ($referenceNode->rgt);
                $data->right_where = 'rgt >= ' . ($referenceNode->rgt);

                $data->new_lft = $referenceNode->rgt;
                $data->new_rgt = $referenceNode->rgt + $nodeWidth - 1;
                $data->new_parent_id = $referenceNode->$k;
                $data->new_level = $referenceNode->level + 1;
                break;

            case 'before':
                $data->left_where = 'lft >= ' . $referenceNode->lft;
                $data->right_where = 'rgt >= ' . $referenceNode->lft;

                $data->new_lft = $referenceNode->lft;
                $data->new_rgt = $referenceNode->lft + $nodeWidth - 1;
                $data->new_parent_id = $referenceNode->parent_id;
                $data->new_level = $referenceNode->level;
                break;

            default:
            case 'after':
                $data->left_where = 'lft > ' . $referenceNode->rgt;
                $data->right_where = 'rgt > ' . $referenceNode->rgt;

                $data->new_lft = $referenceNode->rgt + 1;
                $data->new_rgt = $referenceNode->rgt + $nodeWidth;
                $data->new_parent_id = $referenceNode->parent_id;
                $data->new_level = $referenceNode->level;
                break;
        }

        if ($this->_debug) {
            echo "\nRepositioning Data for $position" . "\n-----------------------------------" . "\nLeft Where:    $data->left_where"
            . "\nRight Where:   $data->right_where" . "\nNew Lft:       $data->new_lft" . "\nNew Rgt:       $data->new_rgt"
            . "\nNew Parent ID: $data->new_parent_id" . "\nNew Level:     $data->new_level" . "\n";
        }

        return $data;
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
            $query->select($this->_tbl_key . ', alias');
            $query->from($this->getTableName());
            $query->where('parent_id = %d');

            // If the table has an ordering field, use that for ordering.
            if (property_exists($this, 'ordering')) {
                $query->order('parent_id, ordering, lft');
            } else {
                $query->order('parent_id, lft');
            }
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
        // If there is no alias or path field, just return true.
        if (!property_exists($this, 'alias') || !property_exists($this, 'path')) {
            return true;
        }

        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // Get the aliases for the path from the node to the root node.
        $query = $db->getQuery(true);
        $query->select('p.alias');
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

    /**
     * Method to create a log table in the buffer optionally showing the query and/or data.
     *
     * @param   boolean  $showData   True to show data
     * @param   boolean  $showQuery  True to show query
     *
     * @return  void
     *
     * @since   11.1
     */
    protected function _logtable($showData = true, $showQuery = true) {
        $db = $this->getDBO();
        $sep = "\n" . str_pad('', 40, '-');
        $buffer = '';
        if ($showQuery) {
            $buffer .= "\n" . $db->getQuery() . $sep;
        }

        if ($showData) {
            $query = $db->getQuery(true);
            $query->select($this->_tbl_key . ', parent_id, lft, rgt, level');
            $query->from($this->getTableName());
            $query->order($this->_tbl_key);
            $db->setQuery($query);

            $rows = $db->loadRowList();
            $buffer .= sprintf("\n| %4s | %4s | %4s | %4s |", $this->_tbl_key, 'par', 'lft', 'rgt');
            $buffer .= $sep;

            foreach ($rows as $row) {
                $buffer .= sprintf("\n| %4s | %4s | %4s | %4s |", $row[0], $row[1], $row[2], $row[3]);
            }
            $buffer .= $sep;
        }
        echo $buffer;
    }

    /**
     * Method to run an update query and check for a database error
     *
     * @param   string  $query         The query.
     * @param   string  $errorMessage  Unused.
     *
     * @return  boolean  False on exception
     *
     * @since   11.1
     */
    protected function _runQuery($query, $errorMessage) {
        $db = $this->getDBO();
        $db->setQuery($query);

        // Check for a database error.
        if (!$db->query()) {
            $e = new JException(JText::sprintf('$errorMessage', get_class($this), $db->getErrorMsg()));
            $this->setError($e);
            $this->_unlock();
            return false;
        }

        if ($this->_debug) {
            $this->_logtable();
        }
    }

    /**
     * Method to update order of table rows
     *
     * @param   array  $idArray    id numbers of rows to be reordered.
     * @param   array  $lft_array  lft values of rows to be reordered.
     *
     * @return  integer  1 + value of root rgt on success, false on failure.
     *
     * @since   11.1
     */
    public function saveorder($idArray = null, $lft_array = null) {
        $db = $this->getDBO();

        // Validate arguments
        if (is_array($idArray) && is_array($lft_array) && count($idArray) == count($lft_array)) {
            for ($i = 0, $count = count($idArray); $i < $count; $i++) {
                // Do an update to change the lft values in the table for each id
                $query = $db->getQuery(true);
                $query->update($this->getTableName());
                $query->where($this->_tbl_key . ' = ' . (int) $idArray[$i]);
                $query->set('lft = ' . (int) $lft_array[$i]);
                $db->setQuery($query);

                // Check for a database error.
                if (!$db->query()) {
                    $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REORDER_FAILED', get_class($this), $db->getErrorMsg()));
                    $this->setError($e);
                    $this->_unlock();
                    return false;
                }

                if ($this->_debug) {
                    $this->_logtable();
                }
            }

            return $this->rebuild();
        } else {
            return false;
        }
    }

}