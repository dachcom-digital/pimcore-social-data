pimcore.registerNS('SocialData.WallsPanel');
SocialData.WallsPanel = Class.create({

    tree: null,
    editPanel: null,
    panel: null,
    panels: {},
    loading: false,

    initialize: function () {

        this.loading = false;
        this.panels = {};

        this.buildLayout();
    },

    getConfig: function () {
        return this.config;
    },

    buildLayout: function () {

        var pimcoreSystemPanel = Ext.getCmp('pimcore_panel_tabs');

        if (this.panel) {
            return this.panel;
        }

        this.panel = new Ext.Panel({
            id: 'social_data_bundle_walls',
            title: t('social_data.settings.walls'),
            border: false,
            iconCls: 'social_data_icon_walls',
            layout: 'border',
            closable: true,
            items: [this.getMainTree(), this.getEditPanel()]
        });

        this.panel.on('destroy', function () {
            pimcore.globalmanager.remove('social_data_bundle_walls');
        }.bind(this));

        pimcoreSystemPanel.add(this.panel);
        pimcoreSystemPanel.setActiveItem('social_data_bundle_walls');

    },

    getMainTree: function () {

        var _self = this,
            store;

        if (this.tree) {
            return this.tree;
        }

        store = Ext.create('Ext.data.TreeStore', {
            proxy: {
                type: 'ajax',
                url: '/admin/social-data/walls/fetch-walls'
            },
            listeners: {
                load: function (tree, records, success, opt) {
                    //new form added, mark as selected!
                    if (opt.wallId !== undefined) {
                        var record = _self.tree.getRootNode().findChild('id', opt.wallId, true);
                        _self.tree.getSelectionModel().select(record);
                    }
                }
            }
        });

        this.tree = new Ext.tree.TreePanel({
            id: 'social_data_walls_tree',
            region: 'west',
            store: store,
            autoScroll: true,
            animate: true,
            containerScroll: true,
            split: true,
            width: 200,
            cls: 'social-data-walls-selector-tree',
            root: {
                draggable: false,
                allowChildren: false,
                id: '0',
                expanded: true
            },
            rootVisible: false,
            listeners: this.getTreeNodeListeners(),
            tbar: {
                items: [
                    {
                        text: t('social_data.wall.add'),
                        iconCls: 'social_data_wall_icon_root_add',
                        handler: this.addNewWall.bind(this)
                    }
                ]
            }
        });

        this.tree.getRootNode().expand();

        return this.tree;
    },

    getEditPanel: function () {

        if (this.editPanel) {
            return this.editPanel;
        }

        this.editPanel = new Ext.TabPanel({
            activeTab: 0,
            items: [],
            region: 'center',
            layout: 'fit',
            listeners: {
                tabchange: function (tabpanel, tab) {
                    var record = this.tree.getRootNode().findChild('id', tab.id, true);
                    this.tree.getSelectionModel().select(record);
                }.bind(this)
            }
        });

        return this.editPanel;
    },

    getTreeNodeListeners: function () {

        return {
            itemclick: this.onTreeNodeClick.bind(this),
            itemcontextmenu: this.onTreeNodeContextMenu.bind(this),
            render: function () {
                this.getRootNode().expand();
            },
            beforeitemappend: function (thisNode, newChildNode) {
                newChildNode.data.qtip = t('id') + ': ' + newChildNode.data.id;
            }
        };
    },

    onTreeNodeClick: function (tree, record) {

        if (!record.isLeaf()) {
            return;
        }

        this.createWallConfigurationPanel(record.data.id);
    },

    onTreeNodeContextMenu: function (tree, record, item, index, e) {

        var menu;

        e.stopEvent();
        tree.select();

        if (!record.isLeaf()) {
            return;
        }

        menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: 'pimcore_icon_delete',
            handler: this.deleteWall.bind(this, tree, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    addNewWall: function () {
        Ext.MessageBox.prompt(
            t('social_data.wall.create_new'),
            t('social_data.wall.create_new_description'),
            this.createNewWall.bind(this),
            null, null, ''
        );
    },

    createNewWall: function (button, value) {

        if (button === 'cancel') {
            return false;
        }

        Ext.Ajax.request({
            url: '/admin/social-data/walls/add-wall',
            method: 'POST',
            params: {
                name: value
            },
            success: function (response) {

                var data = Ext.decode(response.responseText);
                this.tree.getStore().load({'wallId': data.id});

                if (!data || !data.success) {
                    Ext.Msg.alert(t('social_data.wall.create_new'), data.message);
                } else {
                    this.createWallConfigurationPanel(intval(data.id));
                }

            }.bind(this)
        });
    },

    createWallConfigurationPanel: function (id) {

        var wallPanelKey = 'social_data_wall_' + id;

        if (this.loading === true) {
            return;
        }

        if (this.panels[wallPanelKey]) {
            this.panels[wallPanelKey].activate();
        } else {
            this.loading = true;
            this.tree.disable();
            Ext.Ajax.request({
                url: '/admin/social-data/walls/fetch-wall',
                params: {
                    id: id
                },
                success: this.createWallPanel.bind(this)
            });
        }
    },

    createWallPanel: function (response) {

        var responseData = Ext.decode(response.responseText),
            wallPanel, data, wallPanelKey;

        this.loading = false;
        this.tree.enable();

        if (responseData.success === false) {
            Ext.MessageBox.alert(t('error'), t('social_data.wall.loading_error') + responseData.message);
            return;
        }

        data = responseData.data;
        wallPanelKey = 'social_data_wall_' + data.id;

        wallPanel = new SocialData.Wall.MainPanel(data, this);

        this.panels[wallPanelKey] = wallPanel;

        pimcore.layout.refresh();
    },

    deleteWall: function (tree, record) {

        Ext.Msg.confirm(
            t('social_data.wall.delete_title'),
            t('social_data.wall.delete_text'),
            function (btn) {

            if (btn !== 'yes') {
                return;
            }

            Ext.Ajax.request({
                url: '/admin/social-data/walls/delete-wall/' + record.id,
                method: 'POST'
            });

            if (this.panels['social_data_wall_' + record.id]) {
                this.panels['social_data_wall_' + record.id].remove();
            }

            this.getEditPanel().remove(record.id);
            record.remove();

        }.bind(this));
    },

    activate: function () {
        Ext.getCmp('pimcore_panel_tabs').setActiveItem('social_data_bundle_walls');
    }
});