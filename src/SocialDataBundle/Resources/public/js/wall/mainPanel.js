pimcore.registerNS('SocialData.Wall.MainPanel');
SocialData.Wall.MainPanel = Class.create({

    panel: null,
    parentPanel: null,
    formPanel: null,
    feedPanel: null,
    statisticPanel: null,
    logPanel: null,
    feedPanelConfigClasses: null,
    wallData: null,
    wallId: null,
    wallName: null,
    feedStore: null,

    initialize: function (wallData, parentPanel) {

        this.feedPanelConfigClasses = [];
        this.parentPanel = parentPanel;
        this.wallData = wallData;
        this.wallId = wallData.id;
        this.wallName = wallData.name;

        this.feedStore = wallData.stores.feedStore;
        this.feeds = this.wallData.hasOwnProperty('feeds') ? this.wallData.feeds : [];

        this.addLayout();
    },

    remove: function () {
        this.panel.destroy();
    },

    activate: function () {
        this.parentPanel.getEditPanel().setActiveTab(this.panel);
    },

    addLayout: function () {

        var dataStoragePathRelationField,
            assetStoragePathRelationField,
            dataStoragePathRelation,
            assetStoragePathRelation,
            dataStoragePathFieldConfig = {
                label: t('social_data.wall.data_storage_path'),
                id: 'dataStorage',
                config: {
                    types: ['object'],
                    subtypes: {object: ['folder']}
                }
            },
            assetStoragePathFieldConfig = {
                label: t('social_data.wall.asset_storage_path'),
                id: 'assetStorage',
                config: {
                    types: ['asset'],
                    subtypes: {object: ['folder']}
                }
            },
            dataStoragePathValue = this.wallData !== null && this.wallData.hasOwnProperty('dataStorage') ? this.wallData.dataStorage : null,
            assetStoragePathValue = this.wallData !== null && this.wallData.hasOwnProperty('assetStorage') ? this.wallData.assetStorage : null;

        dataStoragePathRelationField = new Formbuilder.extjs.types.href(dataStoragePathFieldConfig, dataStoragePathValue, null);
        dataStoragePathRelation = dataStoragePathRelationField.getHref();
        dataStoragePathRelation.allowBlank = false;

        assetStoragePathRelationField = new Formbuilder.extjs.types.href(assetStoragePathFieldConfig, assetStoragePathValue, null);
        assetStoragePathRelation = assetStoragePathRelationField.getHref();
        assetStoragePathRelation.allowBlank = false;

        this.formPanel = new Ext.form.FormPanel({
            title: false,
            border: false,
            autoScroll: true,
            tools: [
                {
                    xtype: 'tbfill'
                },
                {
                    xtype: 'button',
                    text: t('social_data.wall.dispatch_build_process'),
                    iconCls: 'pimcore_icon_import',
                    handler: this.dispatchWallBuildProcess.bind(this)
                }
            ],
            items: [
                {
                    xtype: 'panel',
                    bodyStyle: 'padding: 10px;',
                    defaults: {
                        labelWidth: 180,
                        width: 400
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            name: 'name',
                            fieldLabel: t('name'),
                            value: this.wallData.name
                        },
                        dataStoragePathRelation,
                        assetStoragePathRelation
                    ]
                },
                this.getStatisticPanel(),
                this.getLogPanel(),
                this.getFeedPanel()
            ]
        });

        this.panel = new Ext.Panel({
            title: this.wallName + ' (ID: ' + this.wallId + ')',
            closable: true,
            cls: 'social-data-wall-panel',
            iconCls: 'social_data_wall_icon',
            autoScroll: true,
            border: false,
            layout: 'fit',
            autoEl: {
                'data-social-data-wall-id': this.wallId
            },
            items: [
                this.formPanel
            ],
            buttons: [
                {
                    text: t('save'),
                    iconCls: 'pimcore_icon_save',
                    handler: this.save.bind(this)
                }
            ],
        });

        this.panel.on({
            beforedestroy: function () {

                if (this.wallId && this.parentPanel.panels['social_data_wall_' + this.wallId]) {
                    delete this.parentPanel.panels['social_data_wall_' + this.wallId];
                }

                if (this.parentPanel.tree.initialConfig !== null &&
                    Object.keys(this.parentPanel.panels).length === 0) {
                    this.parentPanel.tree.getSelectionModel().deselectAll();
                }
            }.bind(this)
        });

        this.parentPanel.getEditPanel().add(this.panel);
        this.parentPanel.getEditPanel().setActiveTab(this.panel);
    },

    dispatchWallBuildProcess: function (btn) {

        btn.setDisabled(true);

        Ext.Ajax.request({
            url: '/admin/social-data/walls/trigger-wall-build-process/' + this.wallId,
            method: 'GET',
            success: function (response) {

                var res = Ext.decode(response.responseText);

                setTimeout(function () {
                    btn.setDisabled(false);
                }, 1000);

                if (res.success === false) {
                    Ext.Msg.alert(t('error'), res.message);
                    return;
                }

                if (res.status === 'locked') {
                    Ext.Msg.alert(t('error'), t('social_data.wall.build_process_locked'));
                    return;
                }

                pimcore.helpers.showNotification(t('success'), t('social_data.wall.build_process_dispatched'), 'success');
            },
            failure: function () {
                btn.setDisabled(false);
            }
        });
    },

    getStatisticPanel: function () {

        var grid;

        grid = new Ext.grid.GridPanel({
            flex: 1,
            style: {
                marginTop: '10px',
                marginBottom: '10px',
            },
            store: new Ext.data.Store({
                fields: ['label', 'value'],
                data: this.wallData.statistics
            }),
            border: true,
            columnLines: true,
            stripeRows: true,
            title: false,
            columns: [
                {
                    text: t('label'),
                    sortable: false,
                    dataIndex: 'label',
                    hidden: false,
                    flex: 2,
                    renderer: function (value) {
                        return t(value);
                    }
                },
                {
                    text: t('value'),
                    sortable: false,
                    dataIndex: 'value',
                    hidden: false,
                    flex: 1
                }
            ]
        });

        this.statisticPanel = new Ext.Panel({
            iconCls: 'pimcore_icon_log',
            collapsible: true,
            collapsed: true,
            bodyStyle: 'padding:0 10px;',
            title: t('social_data.statistic.title'),
            autoScroll: true,
            border: false,
            items: [grid]
        });

        return this.statisticPanel;
    },

    getLogPanel: function () {

        var store, grid, bbar,
            itemsPerPage = pimcore.helpers.grid.getDefaultPageSize(-1);

        store = new Ext.data.Store({
            pageSize: itemsPerPage,
            proxy: {
                type: 'ajax',
                url: '/admin/social-data/logs/fetch-wall-logs/' + this.wallId,
                reader: {
                    type: 'json',
                    rootProperty: 'entries'
                }
            },
            autoLoad: false,
            fields: ['id', 'type', 'message', 'date']
        });

        bbar = pimcore.helpers.grid.buildDefaultPagingToolbar(store, {pageSize: itemsPerPage});

        Ext.Array.each(bbar.query('tbtext'), function (tbTextComp) {
            tbTextComp.setStyle({
                fontSize: 'inherit !important',
                lineHeight: 'inherit !important'
            });
        });

        grid = new Ext.grid.GridPanel({
            flex: 1,
            height: 300,
            style: {
                marginTop: '10px',
                marginBottom: '10px',
            },
            store: store,
            border: true,
            columnLines: true,
            stripeRows: true,
            title: false,
            bbar: bbar,
            listeners: {
                afterrender: function () {
                    store.load();
                }
            },
            columns: [
                {text: 'ID', sortable: false, dataIndex: 'id', hidden: true},
                {text: t('type'), sortable: false, dataIndex: 'type', hidden: false},
                {text: t('message'), sortable: false, dataIndex: 'message', flex: 3, renderer: Ext.util.Format.htmlEncode},
                {text: t('date'), sortable: false, dataIndex: 'date', flex: 1},
            ]
        });

        this.logPanel = new Ext.Panel({
            iconCls: 'pimcore_icon_log',
            collapsible: true,
            collapsed: true,
            bodyStyle: 'padding:0 10px;',
            title: t('social_data.wall.logs'),
            autoScroll: true,
            border: false,
            items: [grid]
        });

        return this.logPanel;
    },

    getFeedPanel: function () {

        this.feedPanel = new Ext.Panel({
            iconCls: 'pimcore_icon_social_data_wall_feed',
            title: t('social_data.wall.feed_configuration'),
            autoScroll: true,
            border: false,
            items: [
                this.getAddControl()
            ]
        });

        this.addFeeds();

        return this.feedPanel;
    },

    getAddControl: function () {

        var classMenu = [],
            items = [],
            availableFeedStore = this.feedStore;

        Ext.Array.each(availableFeedStore, function (feedConfig) {
            classMenu.push({
                text: feedConfig.hasOwnProperty('label') ? feedConfig.label : ('Feed ' + index),
                iconCls: feedConfig.hasOwnProperty('iconCls') ? feedConfig.iconCls : 'pimcore_icon_social_data_wall_feed',
                handler: this.addFeed.bind(this, null, feedConfig)
            });
        }.bind(this));

        if (availableFeedStore.length === 1) {
            items.push({
                cls: 'pimcore_block_button_plus',
                text: t(classMenu[0].text),
                iconCls: 'pimcore_icon_plus',
                handler: classMenu[0].handler
            });
        } else if (availableFeedStore.length > 1) {
            items.push({
                cls: 'pimcore_block_button_plus',
                iconCls: 'pimcore_icon_plus',
                menu: classMenu
            });
        }

        return new Ext.Toolbar({
            items: items
        });
    },

    getDeleteControl: function (data, feedConfig) {

        return new Ext.Toolbar({
            items: [
                {
                    xtype: 'tbtext',
                    html: feedConfig.hasOwnProperty('label') ? '<strong>' + feedConfig.label + '</strong>' : ('Feed ' + index),
                    cls: 'pimcore_icon_social_data_connector_tbtext ' + (feedConfig.hasOwnProperty('iconCls') ? feedConfig.iconCls : 'pimcore_icon_social_data_wall_feed'),
                },
                '->',
                {
                    cls: 'pimcore_block_button_minus',
                    iconCls: 'pimcore_icon_minus',
                    listeners: {
                        'click': this.removeFeed.bind(this)
                    }
                }
            ]
        });
    },

    rebuildFeeds: function (feeds) {

        Ext.Array.each(this.feedPanel.query('panel[cls=feedItem]'), function (cmp) {
            cmp.destroy();
        });

        this.feedPanelConfigClasses = [];
        this.feeds = feeds;

        this.addFeeds();
    },

    addFeeds: function () {

        Ext.Array.each(this.feeds, function (feed) {

            var configuration = Ext.Array.filter(this.feedStore, function (item) {
                return item.identifier === feed.type;
            });

            if (configuration.length !== 1) {
                throw 'invalid or no configuration found';
            }

            this.addFeed(feed, configuration[0]);

        }.bind(this));

    },

    addFeed: function (data, feedConfig) {

        var element,
            items,
            itemLayout,
            feedPanelConfigId = null,
            feedPanelConfig = this.getFeedConfigPanel(data, feedConfig);

        if (feedPanelConfig !== null) {

            feedPanelConfigId = feedPanelConfig.getInternalId();
            itemLayout = feedPanelConfig.getLayout();

            this.feedPanelConfigClasses.push({id: feedPanelConfigId, dataClass: feedPanelConfig});

            this.addSystemConfigPanelToFeed(itemLayout, data);

            items = [itemLayout];

        } else {
            items = [{
                xtype: 'tbtext',
                text: 'No configuration for ' + feedConfig.identifier + ' found.',
            }];
        }

        element = new Ext.Panel({
            style: 'margin: 9px;',
            bodyStyle: 'padding:10px;',
            cls: 'feedItem',
            autoHeight: true,
            border: true,
            id: feedPanelConfigId,
            tbar: this.getDeleteControl(data, feedConfig),
            items: items,
            listeners: {
                afterrender: function (btn) {
                    this.panel.updateLayout();
                }.bind(this)
            }
        });

        this.feedPanel.add(element);
    },

    addSystemConfigPanelToFeed: function (itemLayout, data) {

        itemLayout.insert(0, {
            xtype: 'fieldset',
            title: t('social_data.wall.feed.config'),
            items: [
                {
                    xtype: 'checkbox',
                    value: data && data.hasOwnProperty('persistMedia') ? data.persistMedia : null,
                    fieldLabel: t('social_data.wall.feed.persist_media'),
                    name: 'system.persistMedia',
                    labelAlign: 'left',
                    labelWidth: 250,
                    inputValue: true,
                    uncheckedValue: false
                },
                {
                    xtype: 'checkbox',
                    value: data && data.hasOwnProperty('publishPostImmediately') ? data.publishPostImmediately : true,
                    fieldLabel: t('social_data.wall.feed.publish_post_immediately'),
                    name: 'system.publishPostImmediately',
                    labelAlign: 'left',
                    labelWidth: 250,
                    inputValue: true,
                    uncheckedValue: false
                }
            ]
        });

        return itemLayout;
    },

    removeFeed: function (btn) {

        var feedDataClassToRemove = null,
            panel = btn.up('panel');

        btn.setDisabled(true);

        Ext.each(this.feedPanelConfigClasses, function (feed) {
            if (feed.dataClass.getInternalId() === panel.id) {
                feedDataClassToRemove = feed;
                return false;
            }
        }.bind(this));

        if (feedDataClassToRemove === null) {
            btn.setDisabled(false);
            this.feedPanel.remove(panel);
            return;
        }

        if (feedDataClassToRemove.dataClass.getFeedId() === null) {
            btn.setDisabled(false);
            Ext.Array.remove(this.feedPanelConfigClasses, feedDataClassToRemove);
            this.feedPanel.remove(panel);
            return;
        }

        Ext.Msg.confirm(
            t('social_data.wall.feed.delete_title'),
            t('social_data.wall.feed.delete_text'),
            function (buttonId) {
                btn.setDisabled(false);
                if (buttonId === 'yes') {
                    Ext.Array.remove(this.feedPanelConfigClasses, feedDataClassToRemove);
                    this.feedPanel.remove(panel);
                }
            },
            this
        );
    },

    getFeedConfigPanel: function (data, feedConfig) {

        var connectorEngineId = feedConfig.connectorEngineId,
            connectorName = feedConfig.connectorName,
            feedConfigPanel,
            feedId,
            feedConfiguration;

        if (typeof SocialData.Feed !== 'object') {
            return null;
        }

        if (typeof SocialData.Feed[connectorName] === 'undefined') {
            return null;
        }

        feedId = data && data.hasOwnProperty('id') ? data.id : null;
        feedConfiguration = data && data.hasOwnProperty('configuration') ? data.configuration : null;

        feedConfigPanel = new SocialData.Feed[connectorName](connectorEngineId, this.wallId, feedId, feedConfiguration);

        return feedConfigPanel;
    },

    save: function (ev) {

        var feedData = [],
            hasInvalidConfigFeed = false,
            formData;

        Ext.each(this.feedPanelConfigClasses, function (feed) {

            var feedStorageData = this.generateFeedConfigForPersisting(feed);

            if (feedStorageData === false) {
                hasInvalidConfigFeed = true;
                return false; // break
            } else {
                feedData.push(feedStorageData);
            }

        }.bind(this));

        if (hasInvalidConfigFeed === true) {
            Ext.Msg.alert(t('error'), t('social_data.wall.feed.invalid_configuration'));
            return;
        }

        this.formPanel.setLoading(true);

        formData = {
            name: this.formPanel.getForm().findField('name').getValue(),
            dataStorage: this.formPanel.getForm().findField('dataStorage').getValue(),
            assetStorage: this.formPanel.getForm().findField('assetStorage').getValue(),
            feeds: feedData
        };

        Ext.Ajax.request({
            url: '/admin/social-data/walls/save-wall/' + this.wallId,
            method: 'post',
            params: {
                data: Ext.encode(formData)
            },
            success: this.saveOnComplete.bind(this),
            failure: this.saveOnError.bind(this)
        });
    },

    generateFeedConfigForPersisting: function (feed) {

        var systemData = null,
            transposedConfig,
            transposedData,
            compiledData = {},
            dataClass = feed.dataClass;

        if (!dataClass.isValid()) {
            return false;
        }

        transposedConfig = DataObjectParser.transpose(dataClass.getValues());
        transposedData = transposedConfig.data();

        if (transposedData.hasOwnProperty('system')) {
            systemData = transposedData['system'];
            delete transposedData['system'];
        }

        compiledData['id'] = dataClass.getFeedId();
        compiledData['connectorEngine'] = dataClass.getConnectorEngineId();
        compiledData['configuration'] = transposedData;

        if (Ext.isObject(systemData)) {
            Ext.Object.each(systemData, function (k, v) {
                compiledData[k] = v;
            });
        }

        return compiledData;
    },

    saveOnComplete: function (response) {

        var res = Ext.decode(response.responseText);

        if (res.success === false) {
            pimcore.helpers.showNotification(t('error'), res.message, 'error');
            return;
        }

        this.parentPanel.tree.getStore().load();

        this.rebuildFeeds(res.wall.feeds);

        this.formPanel.setLoading(false);

        pimcore.helpers.showNotification(t('success'), t('social_data.wall.save_successful'), 'success');
    },

    saveOnError: function () {

        this.formPanel.setLoading(false);

        pimcore.helpers.showNotification(t('error'), t('social_data.wall.save_failed'), 'error');
    }
});