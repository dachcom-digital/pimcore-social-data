pimcore.registerNS('SocialData.Feed.AbstractFeed');
SocialData.Feed.AbstractFeed = Class.create({

    connectorEngineId: null,
    wallId: null,
    feedId: null,
    data: null,

    panel: null,

    initialize: function (connectorEngineId, data, wallId) {
        this.connectorEngineId = connectorEngineId;
        this.wallId = wallId;
        this.feedId = data && data.hasOwnProperty('id') ? data.id : null;
        this.data = data && data.hasOwnProperty('configuration') ? data.configuration : null;
    },

    getInternalId: function () {
        return 'feed_' + this.getConnectorEngineId() + '_' + Ext.id();
    },

    getConnectorEngineId: function () {
        return this.connectorEngineId;
    },

    getWallId: function () {
        return this.wallId;
    },

    getFeedId: function () {
        return this.feedId;
    },

    /**
     * @abstract
     */
    isValid: function () {
        return false;
    },

    /**
     * @abstract
     */
    getValues: function () {
        return null;
    },
});