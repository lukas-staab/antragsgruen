define(["require","exports"],function(t,e){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=function(){function t(t,e,a){var i=this;if(this.$form=t,this.$draftHint=e,this.isChanged=!1,this.$html=$("html"),this.$html.hasClass("localstorage")){this.localKey=a+"_"+Math.floor(1e6*Math.random());var n;t.append('<input type="hidden" name="draftId" value="'+this.localKey+'">');for(n in localStorage)if(localStorage.hasOwnProperty(n)&&0==n.indexOf(a+"_")){var r=JSON.parse(localStorage.getItem(n)),o=new Date(r.lastEdit),s=$("<li><a href='#' class='restore'></a> <a href='#' class='delete glyphicon glyphicon-trash' title='"+__t("std","draft_del")+"'></a></li>");s.data("key",n);var d=new Intl.DateTimeFormat(this.$html.attr("lang"),{weekday:"long",year:"numeric",month:"long",day:"numeric",hour:"numeric",minute:"numeric"}).format(o);s.find(".restore").text("Entwurf vom: "+d).click(function(t){t.preventDefault();var e=$(t.delegateTarget).parents("li").first();bootbox.confirm(__t("std","draft_restore_confirm"),function(t){t&&i.doRestore(e)})}),s.find(".delete").click(function(t){t.preventDefault();var e=$(t.delegateTarget).parents("li").first();bootbox.confirm(__t("std","draft_del_confirm"),function(t){t&&i.doDelete(e)})}),this.$draftHint.find("ul").append(s),this.$draftHint.removeClass("hidden")}window.setTimeout(this.saveInitialData.bind(this),2e3),window.setInterval(this.doBackup.bind(this),3e3)}}return t.prototype.saveInitialData=function(){for(var t in CKEDITOR.instances)CKEDITOR.instances.hasOwnProperty(t)&&$("#"+t).data("original",CKEDITOR.instances[t].getData());$(".form-group.plain-text").each(function(){var t=$(this).find("input[type=text]");t.data("original",t.val())}),$(".form-group.amendmentStatus").each(function(){var t=$(this).find("input[type=text].hidden");t.data("original",t.val())})},t.prototype.doBackup=function(){var t,e={},a=!1;for(t in CKEDITOR.instances)if(CKEDITOR.instances.hasOwnProperty(t)){var i=CKEDITOR.instances[t].getData();e[t]=i,i!=$("#"+t).data("original")&&(a=!0)}$(".form-group.plain-text").each(function(){var t=$(this).find("input[type=text]");e[t.attr("id")]=t.val(),t.val()!=t.data("original")&&(a=!0)}),$(".form-group.amendmentStatus").each(function(){var t=$(this).find("input[type=text].hidden");e[$(this).find(".selectlist").attr("id")]=t.val(),t.val()!=t.data("original")&&(a=!0)}),a?(e.lastEdit=(new Date).getTime(),localStorage.setItem(this.localKey,JSON.stringify(e)),this.isChanged=!0):(localStorage.removeItem(this.localKey),this.isChanged=!1)},t.prototype.doDelete=function(t){localStorage.removeItem(t.data("key")),t.remove(),0==this.$draftHint.find("ul").children().length&&this.$draftHint.addClass("hidden")},t.prototype.doRestore=function(t){var e,a=t.data("key"),i=JSON.parse(localStorage.getItem(a));for(e in CKEDITOR.instances)CKEDITOR.instances.hasOwnProperty(e)&&void 0!==i[e]&&CKEDITOR.instances[e].setData(i[e]);i.hasOwnProperty("amendmentEditorial_wysiwyg")&&""!=i.amendmentEditorial_wysiwyg&&(CKEDITOR.hasOwnProperty("amendmentEditorial_wysiwyg")||($(".editorialChange .opener").click(),window.setTimeout(function(){CKEDITOR.instances.amendmentEditorial_wysiwyg.setData(i.amendmentEditorial_wysiwyg)},100))),$(".form-group.plain-text").each(function(t,e){var a=$(e).find("input[type=text]");void 0!==i[a.attr("id")]&&a.val(i[a.attr("id")])}),$(".form-group.amendmentStatus").each(function(t,e){var a=$(e).find(".selectlist").attr("id");void 0!==i[a]&&$("#"+a).selectlist("selectByValue",i[a])}),this.$form.find("input[name=draftId]").remove(),this.$form.append('<input type="hidden" name="draftId" value="'+a+'">'),this.localKey=a,t.remove(),0==this.$draftHint.find("ul").children().length&&this.$draftHint.addClass("hidden")},t.prototype.hasChanges=function(){return this.isChanged},t}();e.DraftSavingEngine=a});
//# sourceMappingURL=DraftSavingEngine.js.map
