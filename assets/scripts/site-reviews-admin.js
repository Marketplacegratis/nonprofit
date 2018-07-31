!function(e){"use strict";GLSR.Ajax=function(t,i){this.event=i||null,this.notice=null,this.request=t},GLSR.Ajax.prototype={t:function(t){this.i(t);var i={action:GLSR.action,ajax_request:!0};return i[GLSR.nameprefix]=this.request,i},i:function(t){this.request.nonce||(GLSR.nonce[this.request.action]?this.request.nonce=GLSR.nonce[this.request.action]:t&&(this.request.nonce=t.closest("form").find("#_wpnonce").val()))},s:function(i,s){var n=this;e.post(GLSR.ajaxurl,this.t(s)).done(function(t){n.notice=t.data.notices||null,"function"==typeof i&&i(t.data,t.success),s&&s.prop("disabled",!1)}).always(function(){n.notice&&GLSR.Notices(n.notice)})},n:function(t){this.event?this.e(t):this.s(t)},e:function(t){this.event.preventDefault();var i=e(this.event.currentTarget);i.is(":disabled")||(i.prop("disabled",!0),this.s(t,i))}}}(jQuery),function(t){"use strict";GLSR.ColorPicker=function(){"object"==typeof t.wp&&"function"==typeof t.wp.wpColorPicker&&t(document).find("input[type=text].color-picker-hex").each(function(){t(this).wpColorPicker(t(this).data("colorpicker")||{})})}}(jQuery),function(){"use strict";GLSR.Forms=function(t){this.el=document.querySelector(t),this.el&&(this.depends=this.el.querySelectorAll("[data-depends]"),this.depends.length&&this.h())},GLSR.Forms.prototype={h:function(){for(var t=this.el.elements,i=0;i<t.length;i++)-1!==["INPUT","SELECT"].indexOf(t[i].nodeName)&&t[i].addEventListener("change",this.o.bind(this))},c:function(t,i){return"checkbox"===t.type?!!t.checked:Array.isArray(i.value)?-1!==this.u(i.value).indexOf(this.r(t.value)):this.r(i.value)===this.r(t.value)},r:function(t){return-1!==["true","on","yes","1"].indexOf(t)||-1===["false","off","no","0"].indexOf(t)&&t},u:function(t){return t.map(this.r)},o:function(n){this.depends.forEach(function(t){var i=t.getAttribute("data-depends");if(i){var s;try{s=JSON.parse(i)}catch(t){return console.log(i),console.error(t)}s.name===n.currentTarget.name.replace("[]","")&&this.a(t,this.c(n.currentTarget,s))}}.bind(this))},a:function(t,i){var s=t.closest(".glsr-field");s&&s.classList[i?"remove":"add"]("hidden")}}}(),function(i){"use strict";GLSR.Notices=function(t){t&&(i("#glsr-notices").length||(i("#message.notice").remove(),i("form#post").before('<div id="glsr-notices" />')),i("#glsr-notices").html(t),i(document).trigger("wp-updates-notice-added"))}}(jQuery),function(s){"use strict";GLSR.Pinned=function(){this.el=s("#pinned-status-select"),this.el&&(this.cancel=s("a.cancel-pinned-status"),this.cancel.on("click",this.l.bind(this)),this.edit=s("a.edit-pinned-status"),this.edit.on("click",this.f.bind(this)),this.save=s("a.save-pinned-status"),this.save.on("click",this.d.bind(this))),s("table td.pinned i").on("click",this.p.bind(this))},GLSR.Pinned.prototype={S:function(){this.el.slideUp("fast"),this.edit.show().focus()},l:function(t){t.preventDefault(),this.S(),this.el.find("select").val("0"===s("#hidden-pinned-status").val()?1:0)},f:function(t){t.preventDefault(),this.el.is(":hidden")&&(this.el.slideDown("fast",function(){this.el.find("select").focus()}.bind(this)),this.edit.hide())},d:function(t){t.preventDefault(),this.S(),this.target=t.currentTarget;var i={action:"toggle-pinned",id:s("#post_ID").val(),pinned:s("#pinned-status").val()};new GLSR.Ajax(i).n(this.R.bind(this))},p:function(t){t.preventDefault(),this.target=t.currentTarget;var i={action:"toggle-pinned",id:t.currentTarget.getAttribute("data-id")};new GLSR.Ajax(i).n(this.m.bind(this))},R:function(t){s("#pinned-status").val(0|!t.pinned),s("#hidden-pinned-status").val(0|t.pinned),s("#pinned-status-text").text(t.pinned?this.target.dataset.yes:this.target.dataset.no),GLSR.Notices(t.notices)},m:function(t){this.target.classList[t.pinned?"add":"remove"]("pinned")}}}(jQuery),function(i){"use strict";GLSR.Pointers=function(){i.each(GLSR.pointers,function(t,i){this.h(i)}.bind(this))},GLSR.Pointers.prototype={v:function(t){i.post(GLSR.ajaxurl,{action:"dismiss-wp-pointer",pointer:t})},h:function(t){i(t.target).pointer({content:t.options.content,position:t.options.position,close:this.v.bind(t.id)}).pointer("open").pointer("sendToTop"),i(document).on("wp-window-resized",function(){i(t.target).pointer("reposition")})}}}(jQuery),function(t,s,e){"use strict";GLSR.Search=function(t,i){this.el=e(t),this.options=i,this.searchTerm=null,this.h()},GLSR.Search.prototype={defaults:{action:null,exclude:[],onInit:null,onResultClick:null,results:{},selected:-1,selectedClass:"glsr-selected-result",selectorEntries:".glsr-strings-table tbody",selectorResults:".glsr-search-results",selectorSearch:".glsr-search-input"},h:function(){this.options=e.extend({},this.defaults,this.options),this.el.length&&(this.options.entriesEl=this.el.parent().find(this.options.selectorEntries),this.options.resultsEl=this.el.find(this.options.selectorResults),this.options.searchEl=this.el.find(this.options.selectorSearch),this.options.searchEl.attr("aria-describedby","live-search-desc"),"function"==typeof this.options.onInit&&this.options.onInit.call(this),this.g())},g:function(){this.options.searchEl.on("input",t.debounce(this.L.bind(this),500)),this.options.searchEl.on("keyup",this.G.bind(this)),this.options.searchEl.on("keydown keypress",function(t){GLSR.keys.ENTER===t.which&&t.preventDefault()}),e(document).on("click",this.y.bind(this)),e(document).on("keydown",this.w.bind(this))},k:function(){void 0!==this.searchRequest&&this.searchRequest.abort()},b:function(){this.k(),this.options.resultsEl.empty(),this.el.removeClass("is-active"),e("body").removeClass("glsr-focus")},C:function(t){var i=this.options.entriesEl.children("tr").eq(t),s=this;i.find("td").css({backgroundColor:"#faafaa"}),i.fadeOut(350,function(){e(this).remove(),s.options.results={},s.T(),s.x()})},j:function(t){e("body").addClass("glsr-focus"),this.options.resultsEl.append(t),this.options.resultsEl.children("span").on("click",this.Q.bind(this))},z:function(){this.options.entriesEl.on("click","a.delete",this.D.bind(this)),this.options.entriesEl.sortable({items:"tr",tolerance:"pointer",start:function(t,i){i.placeholder.height(i.helper[0].scrollHeight)},sort:function(t,i){var s=t.pageY-e(this).offsetParent().offset().top-i.helper.outerHeight(!0)/2;i.helper.css({top:s+"px"})}})},I:function(t){this.options.selected+=t,this.options.results.removeClass(this.options.selectedClass),this.options.selected<0&&(this.options.selected=-1,this.options.searchEl.focus()),this.options.selected>=this.options.results.length&&(this.options.selected=this.options.results.length-1),0<=this.options.selected&&this.options.results.eq(this.options.selected).addClass(this.options.selectedClass).focus()},y:function(t){e(t.currentTarget).find(this.el).length&&e("body").hasClass("glsr-focus")&&this.b()},w:function(t){if(!e.isEmptyObject(this.options.results)){if(GLSR.keys.ESC===t.which&&this.b(),GLSR.keys.ENTER===t.which||GLSR.keys.SPACE===t.which){var i=this.options.resultsEl.find("."+this.options.selectedClass);i&&i.trigger("click")}GLSR.keys.UP===t.which&&(t.preventDefault(),this.I(-1)),GLSR.keys.DOWN===t.which&&(t.preventDefault(),this.I(1))}},D:function(t){t.preventDefault(),this.C(e(t.currentTarget).closest("tr").index())},Q:function(t){t.preventDefault(),"function"==typeof this.options.onResultClick&&this.options.onResultClick.call(this,t),this.b()},L:function(t){if(this.k(),this.searchTerm===t.currentTarget.value&&this.options.results.length)return this.j(this.options.results);if(this.options.resultsEl.empty(),this.options.selected=-1,this.searchTerm=t.currentTarget.value,""===this.searchTerm)return this.P();this.el.addClass("is-active");var i={};i[GLSR.nameprefix]={action:this.options.action,exclude:this.options.exclude,nonce:this.el.find("#_search_nonce").val(),search:this.searchTerm},this.searchRequest=s.ajax.post(GLSR.action,i).done(function(t){this.el.removeClass("is-active"),this.j(t.items?t.items:t.empty),this.options.results=this.options.resultsEl.children(),delete this.searchRequest}.bind(this))},G:function(t){GLSR.keys.ESC===t.which&&this.P(),GLSR.keys.ENTER===t.which&&(this.L(t),t.preventDefault())},A:function(t){t.preventDefault();var i=this.el.find(".description");this.el.find("input#assigned_to").val(""),i.find("a").css({color:"#c00"}),i.fadeOut("fast",function(){e(this).html("").show()})},T:function(){var n=this;this.options.exclude=[],this.options.entriesEl.children("tr").each(function(s){e(this).find(".glsr-string-td2").children().filter(":input").each(function(){var t=e(this),i=t.attr("name").replace(/\[\d+\]/i,"["+s+"]");t.attr("name",i),t.is("[data-id]")&&n.options.exclude.push({id:t.val()})})})},P:function(){this.b(),this.options.results={},this.options.searchEl.val("")},x:function(){var t=0<this.options.entriesEl.children().length?"remove":"add";this.options.entriesEl.parent()[t+"Class"]("glsr-hidden")}}}(window._,window.wp,jQuery),function(i){"use strict";GLSR.Shortcode=function(t){this.el=document.querySelector(t),this.el&&(this.current=null,this.editor=null,this.button=this.el.querySelector("button"),this.menuItems=this.el.querySelectorAll(".mce-menu-item"),this.button&&this.menuItems.length&&(this.create=function(t){if(this.editor=tinymce.get(t),this.editor){var i={action:"mce-shortcode",shortcode:this.current};new GLSR.Ajax(i).n(this.N.bind(this))}},this.h()))},GLSR.Shortcode.prototype={V:{},H:[],h:function(){document.addEventListener("click",this.K.bind(this)),this.button.addEventListener("click",this.U.bind(this)),this.menuItems.forEach(function(t){t.addEventListener("click",this.F.bind(this))}.bind(this))},O:function(){tinymce.execCommand("GLSR_Shortcode")},W:function(){i("#scTemp").length?this.O():(i("body").append('<textarea id="scTemp" style="display:none!important;"/>'),tinymce.init({elements:"scTemp",mode:"exact",plugins:["glsr_shortcode","wplink"]}),setTimeout(function(){this.O()}.bind(this),200))},v:function(){i(this.button).removeClass("active"),i(this.el).find(".glsr-mce-menu").hide()},q:function(){var t=i("#scTemp");t.length&&(tinymce.get("scTemp").remove(),t.remove())},N:function(t){if(t){if(0===t.body.length)return window.send_to_editor("["+t.shortcode+"]"),void this.q();var i=this.B(t);t.ok.constructor===Array&&(i.buttons[0].text=t.ok[0],i.buttons[0].onclick="close",delete i.buttons[1]),this.editor.windowManager.open(i)}},J:function(t){for(var i in this.V=t,this.H=[],t)t.hasOwnProperty(i)&&(this.M(i),this.X(i),this.Y(i));this.V.hide=this.H.join(",")},M:function(t){"count"!==t||i.isNumeric(this.V[t])||(this.V[t]="")},X:function(t){if(GLSR.hiddenkeys.hasOwnProperty(this.current)){var i=t.substring("hide_".length);-1!==GLSR.hiddenkeys[this.current].indexOf(i)&&(this.V[t]&&this.H.push(i),delete this.V[t])}},Y:function(t){"id"===t&&(this.V[t]=(+new Date).toString(36))},K:function(t){i(t.currentTarget).closest(i(this.el)).length||this.v()},U:function(t){t.preventDefault(),t.currentTarget.classList.contains("active")?this.v():this.Z()},F:function(t){t.preventDefault(),this.current=t.currentTarget.dataset.shortcode,this.current&&(tinymce.get(window.wpActiveEditor)?this.O():this.W(),setTimeout(function(){this.v()}.bind(this),100))},Z:function(){i(this.button).addClass("active"),i(this.el).find(".glsr-mce-menu").show()},$:function(t){return[{classes:"btn glsr-btn primary",onclick:this.tt.bind(this),text:t.ok},{onclick:"close",text:t.close}]},B:function(t){return{title:t.title,body:t.body,classes:"glsr-mce-popup",minWidth:320,buttons:this.$(t),onsubmit:this.it.bind(this,t),onclose:this.q.bind(this)}},it:function(t,i){var s="";for(var n in this.J(i.data),this.V)this.V.hasOwnProperty(n)&&""!==this.V[n]&&(s+=" "+n+'="'+this.V[n]+'"');window.send_to_editor("["+t.shortcode+s+"]")},tt:function(){var t=this.editor.windowManager.getWindows()[0];this.st(t)&&t.submit()},st:function(t){var i,s=!0,n=GLSR.shortcodes[this.current];for(var e in n)if(void 0!==(i=t.find("#"+e)[0])&&""===i.state.data.value){s=!1,alert(n[e]);break}return s}}}(jQuery),function(e){"use strict";GLSR.Status=function(t){var i=document.querySelectorAll(t);i.length&&i.forEach(function(t){t.addEventListener("click",this.nt)}.bind(this))},GLSR.Status.prototype={nt:function(s){var t=s.currentTarget.href.match(/post=([0-9]+)/),i=s.currentTarget.href.match(/action=([a-z]+)/);if(null!==t&&null!==i){var n={action:"change-status",nonce:GLSR.nonce["change-status"],post_id:t[1],status:i[1]};new GLSR.Ajax(n,s).n(function(t){if(t.class){var i=e(s.currentTarget);i.closest("tr").removeClass("status-pending status-publish").addClass(t.class),i.closest("td.column-title").find("strong").html(t.link)}})}}}}(jQuery),function(i){"use strict";GLSR.Tabs=function(t){this.options=i.extend({},this.defaults,t),this.active=document.querySelector("input[name=_active_tab]"),this.referrer=document.querySelector("input[name=_wp_http_referer]"),this.tabs=document.querySelectorAll(this.options.tabSelector),this.views=document.querySelectorAll(this.options.viewSelector),this.active&&this.referrer&&this.tabs&&this.views&&this.h()},GLSR.Tabs.prototype={defaults:{tabSelector:".glsr-nav-tab",viewSelector:".glsr-nav-view"},h:function(){i(window).on("hashchange",this.et.bind(this)),[].forEach.call(this.tabs,function(t,i){(location.hash?t.getAttribute("href").slice(1)===location.hash.slice(2):0===i)&&this.ht(t),t.addEventListener("click",this.nt.bind(this)),t.addEventListener("touchend",this.nt.bind(this))}.bind(this))},ot:function(t){return t?"add":"remove"},nt:function(t){t.preventDefault(),t.currentTarget.blur(),this.ht(t.currentTarget),location.hash="!"+t.currentTarget.getAttribute("href").slice(1)},et:function(){for(var t=location.hash.split("#!")[1],i=0;i<this.views.length;i++)if(t===this.views[i].id){this.ht(this.tabs[i]);break}},ct:function(t){var i=this.referrer.value.split("#")[0]+"#!"+this.views[t].id;this.referrer.value=i},ht:function(n){[].forEach.call(this.tabs,function(t,i){var s=this.ot(t===n);"add"===s&&(this.active.value=this.views[i].id,this.ct(i),this.ut(i)),t.classList[s]("nav-tab-active")}.bind(this))},ut:function(n){[].forEach.call(this.views,function(t,i){var s=this.ot(i!==n);t.classList[s]("ui-tabs-hide")}.bind(this))}}}(jQuery),function(i){"use strict";GLSR.TextareaResize=function(){var t=document.querySelector("#contentdiv > textarea");t&&(this.rt(t),i(document).on("wp-window-resized.editor-expand",function(){this.rt(t)}.bind(this)))},GLSR.TextareaResize.prototype={rt:function(t){var i=320<t.scrollHeight?t.scrollHeight:320;t.style.height="auto",t.style.height=i+"px"}}}(jQuery),function(s){"use strict";GLSR.Tools=function(){s("form").on("click","#clear-console",this.at,this.nt),s("form").on("click","#fetch-console",this.at,this.nt),s("form").on("click","#count-reviews",this.nt)},GLSR.Tools.prototype={at:function(t,i){i&&s("#log-file").val(t.console)},nt:function(s){var t={action:s.currentTarget.name};new GLSR.Ajax(t,s).n(function(t,i){"function"==typeof s.data&&s.data(t,i)})}}}(jQuery),GLSR.keys={DOWN:40,ENTER:13,ESC:27,SPACE:32,UP:38},jQuery(function(e){GLSR.shortcode=new GLSR.Shortcode(".glsr-mce"),GLSR.ColorPicker(),new GLSR.Forms("form.glsr-form"),new GLSR.Pinned,new GLSR.Pointers,new GLSR.Search("#glsr-search-posts",{action:"search-posts",onInit:function(){this.el.on("click",".glsr-remove-button",this.A.bind(this))},onResultClick:function(t){var i=e(t.currentTarget),s=wp.template("glsr-assigned-post"),n={url:i.data("url"),title:i.text()};s&&(this.el.find("input#assigned_to").val(i.data("id")),this.el.find(".description").html(s(n)),this.el.on("click",".glsr-remove-button",this.A.bind(this)),this.P())}}),new GLSR.Search("#glsr-search-translations",{action:"search-translations",onInit:function(){this.z()},onResultClick:function(t){var s=e(t.currentTarget),i=s.data("entry"),n=wp.template("glsr-string-"+(i.p1?"plural":"single"));i.index=this.options.entriesEl.children().length,i.prefix=this.options.resultsEl.data("prefix"),n&&(this.options.entriesEl.append(n(i)),this.options.exclude.push({id:i.id}),this.options.results=this.options.results.filter(function(t,i){return i!==s.get(0)})),this.x()}}),new GLSR.Status("a.glsr-change-status"),new GLSR.Tabs,new GLSR.TextareaResize,new GLSR.Tools});