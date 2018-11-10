(function() {
  tinymce.create('tinymce.plugins.MWD_mce', {
    init : function(ed, url) {
      ed.addCommand('mceMWD_mce', function() {
        ed.windowManager.open({
          file : forms_admin_ajax,
          width : 300 + ed.getLang('MWD_mce.delta_width', 0),
          height : 155 + ed.getLang('MWD_mce.delta_height', 0),
          inline : 1,
          title: 'MailChimp Form'
        }, {
          plugin_url : url // Plugin absolute URL
        });
      });
      ed.addButton('MWD_mce', {
        title : 'Insert MailChimp Form',
        cmd : 'mceMWD_mce',
        image: url.replace('/js', '') + '/images/mwd_mailchimp_button.png'
      });
    }
  });
  tinymce.PluginManager.add('MWD_mce', tinymce.plugins.MWD_mce);
})();