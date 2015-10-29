/**
 * This class is the main view for the application. It is specified in app.js as the
 * "autoCreateViewport" property. That setting automatically applies the "viewport"
 * plugin to promote that instance of this class to the body element.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('UserActivityHistogram.view.main.MainPanelController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Ext.window.MessageBox'
    ],

    alias: 'controller.main-panel',

    'searchUsername': function(btn){
        // disable the search button when processing the request.
        btn.disable();
        var tu = this.lookupReference('twitterUsername'),
            ts = Ext.data.StoreManager.get('Tweet'),
            chart = this.lookupReference('chart');
        if(tu.validate()){
            chart.mask('loading...');
            // text field is valid
            // load the store to fetch states.
            ts.load({
                params: {
                    twitterUsername: tu.getValue()
                },
                callback: function(){
                    // enable to the search button after fetch is completed.
                    btn.enable();
                    chart.unmask();
                }
            });
            // update chart title with the given twitter username
        }else{
            // text field is invalid
            // enable to the search button
            btn.enable();
        }

    }
});
