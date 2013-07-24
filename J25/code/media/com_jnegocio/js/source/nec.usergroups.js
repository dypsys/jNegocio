/*
 * @version	$Id$
 * @package	Joomla
 * @subpackage	jNegocio
 * @copyright	Copyright (C) 2005 - 2012 CESI Informàtica i comunicions. All rights reserved.
 * @license	Comercial License
 */
var Negocio = Negocio || {};
Negocio.usergroups = {};

Negocio.usergroups.App = new Class({
    Implements: [Events, Options],
    usergroups: [],
    options: {
        usergroups: [],
        defaultUsergroupId: ''
    },
    initialize: function(options) {
        this.setOptions(options);
        this.addUserGroups(this.options.usergroups);
    },
    addUserGroups: function(items) {
        $$(items).each(function(item) {
            var row = new Negocio.UserGroup(item);
            this.addUserGroup(row);
        }, this);
    },
    addUserGroup: function(item) {
        this.usergroups[item.id] = item;
    },
    getUserGroup: function(usergroup_id) {
        return this.usergroups[usergroup_id];
    },
    setDefaultUserGroupID: function(usergroup_id) {
        this.options.defaultUsergroupId = usergroup_id;
    },
    getDefaultUserGroupID: function() {
        return this.options.defaultUsergroupId;
    },
    getUserGroups: function() {
        return this.usergroups;
    }
});

Negocio.UserGroup = new Class({
    Implements: [Options],
    id: 0,
    name: 0,
    initialize: function(object, options) {
        this.setOptions(options);
        $each(object, function(value, key) {
            this[key] = value;
        }.bind(this));
    }
});