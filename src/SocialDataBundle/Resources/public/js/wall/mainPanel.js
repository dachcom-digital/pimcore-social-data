pimcore.registerNS('SocialData.Wall.MainPanel');
SocialData.Wall.MainPanel = Class.create({

    parentPanel: null,

    formPanel: null,
    panel: null,

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
            defaults: {
                labelWidth: 250
            },
            items: [
                {
                    xtype: 'panel',
                    bodyStyle: 'padding: 10px;',
                    items: [
                        {
                            xtype: 'textfield',
                            width: 600,
                            name: 'name',
                            fieldLabel: t('name'),
                            value: this.wallData.name
                        },
                        dataStoragePathRelation,
                        assetStoragePathRelation
                    ]
                },
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

    getFeedPanel: function () {

        this.feedPanel = new Ext.Panel({
            iconCls: 'pimcore_icon_social_data_wall_feed',
            bodyStyle: 'padding:10px;',
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

        var items = [{
            xtype: 'tbtext',
            html: feedConfig.hasOwnProperty('label') ? '<strong>' + feedConfig.label + '</strong>' : ('Feed ' + index),
            iconCls: feedConfig.hasOwnProperty('iconCls') ? feedConfig.iconCls : 'pimcore_icon_social_data_wall_feed',
        }];

        items.push('->');

        items.push({
            cls: 'pimcore_block_button_minus',
            iconCls: 'pimcore_icon_minus',
            listeners: {
                'click': this.removeFeed.bind(this)
            }
        });

        return new Ext.Toolbar({
            items: items
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

            itemLayout.insert(0, {
                xtype: 'fieldset',
                title: t('social_data.wall.feed.config'),
                items: [
                    {
                        xtype: 'checkbox',
                        value: data.hasOwnProperty('persistMedia') ? data.persistMedia : null,
                        fieldLabel: t('social_data.wall.feed.persist_media'),
                        name: 'persistMedia',
                        labelAlign: 'left',
                        anchor: '100%',
                        flex: 1
                    }
                ]
            })

            items = [itemLayout];

        } else {
            items = [{
                xtype: 'tbtext',
                text: 'No configuration for ' + feedConfig.identifier + ' found.',
            }]
        }

        element = new Ext.Panel({
            style: 'margin-top: 10px;',
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

    removeFeed: function (btn) {
        var panel = btn.up('panel');

        Ext.each(this.feedPanelConfigClasses, function (feed) {
            if (feed.id === panel.id) {
                Ext.Array.remove(this.feedPanelConfigClasses, feed);
                return false;
            }
        }.bind(this));

        this.feedPanel.remove(panel);
    },

    getFeedConfigPanel: function (data, feedConfig) {

        var connectorEngineId = feedConfig.connectorEngineId,
            connectorName = feedConfig.connectorName,
            feedConfigPanel;

        if (typeof SocialData.Feed !== 'object') {
            return null;
        }

        if (typeof SocialData.Feed[connectorName] === 'undefined') {
            return null;
        }

        feedConfigPanel = new SocialData.Feed[connectorName](connectorEngineId, data, this.wallId);

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

        var persistMedia = null,
            transposedConfig,
            transposedData,
            compiledData = {},
            dataClass = feed.dataClass;

        if (!dataClass.isValid()) {
            return false;
        }

        transposedConfig = DataObjectParser.transpose(dataClass.getValues());
        transposedData = transposedConfig.data();

        // @todo: improve feed system config fetching here!

        if (transposedData.hasOwnProperty('persistMedia')) {
            persistMedia = transposedData['persistMedia'];
            delete transposedData['persistMedia'];
        }

        compiledData['configuration'] = transposedData;
        compiledData['persistMedia'] = persistMedia;
        compiledData['id'] = dataClass.getFeedId();
        compiledData['connectorEngine'] = dataClass.getConnectorEngineId();

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