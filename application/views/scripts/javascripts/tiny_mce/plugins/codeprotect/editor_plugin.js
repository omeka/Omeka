/****
 * Original codeprotect by Tijmen Schep, Holland, 9-10-2005
 * Updated for Tinymce 3.x by Greg Smith, UK, 19-02-2008
 ****/

(function() {
		  
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('codeprotect');

	tinymce.create('tinymce.plugins.CodeprotectPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
        init : function(ed, url) {
            
            ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/<\?/gi, "<!--CODE");
                o.content = o.content.replace(/\?>/gi, "CODE-->");
				
				//Fixes URL Encoding---
				//code protect html source fix
				o.content = o.content.replace(/<\?/gi, "&lt;!--CODE");
				o.content = o.content.replace(/\?>/gi, "CODE--&gt;");
				//firefox fix
				o.content = o.content.replace(/&amp;quot;mceNonEditable&amp;quot;/gi, "mceNonEditable");
				//url encoding fix
				o.content = o.content.replace(/'/gi, "'");
				o.content = o.content.replace(/&quot;/gi, '"');
				//End Fixes URL Encoding---
				
				
            });
           
            ed.onPostProcess.add(function(ed, o) {
                if (o.get) {
                    o.content = o.content.replace(/<!--CODE/gi, "<?");
                    o.content = o.content.replace(/CODE-->/gi, "?>");
					
					//Fixes URL Encoding---
					//code protect html source fix
					o.content = o.content.replace(/&lt;!--CODE/gi, "<?");
					o.content = o.content.replace(/CODE--&gt;/gi, "?>");
					o.content = o.content.replace(/&lt;\?/gi, "<?");
					o.content = o.content.replace(/\?&gt;/gi, "?>");
					//firefox javascript mceNonEditable insert fix
					o.content = o.content.replace(/&amp;quot;mceNonEditable&amp;quot;/gi, "mceNonEditable");
					//url encoding fix
					o.content = o.content.replace(/'/gi, "'");
					o.content = o.content.replace(/&quot;/gi, '"');
					//End Fixes URL Encoding---
					
					
                }
            });
           
        },

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'CodeProtect plugin',
				author : 'Greg Smith (Updated from original by Tijmen Schep)',
				authorurl : 'http://www.hotpebble.com',
				infourl : '',
				version : "1.1"
			};
		}
				
	});

	// Register plugin
	tinymce.PluginManager.add('codeprotect', tinymce.plugins.CodeprotectPlugin);
	
})();
