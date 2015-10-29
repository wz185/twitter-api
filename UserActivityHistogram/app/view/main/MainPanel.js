/**
 * This class is the main view for the application. It is specified in app.js as the
 * "autoCreateViewport" property. That setting automatically applies the "viewport"
 * plugin to promote that instance of this class to the body element.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('UserActivityHistogram.view.main.MainPanel', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Ext.panel.Panel',
        'UserActivityHistogram.view.main.MainPanelController',
        'UserActivityHistogram.view.main.SearchFilter',
        'UserActivityHistogram.view.main.UserHistogram'
    ],

    xtype: 'app-main',

    controller: 'main-panel',

    layout: {
        type: 'anchor'
    },

    defaults: {
        margin: 50
    },

    items: [
        {
            xtype: 'search-filter'
        },
        {
            xtype: 'user-histogram'
        }
    ]
});
