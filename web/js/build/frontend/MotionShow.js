define(["require","exports","./LineNumberHighlighting","../shared/MotionInitiatorShow"],(function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});class i{constructor(e){this.$element=e,this.activeAmendmentId=null,this.$paraFirstLine=e.find(".lineNumber").first(),this.lineHeight=this.$paraFirstLine.height();let t=e.find(".bookmarks > .amendment");t=t.sort((function(e,t){return $(e).data("first-line")-$(t).data("first-line")})),e.find(".bookmarks").append(t),e.find("ul.bookmarks li.amendment").each(((e,t)=>{this.initInlineAmendmentPosition($(t)),this.toggleInlineAmendmentBehavior($(t))}))}initInlineAmendmentPosition(e){let t=(e.data("first-line")-this.$paraFirstLine.data("line-number"))*this.lineHeight,n=e.prevAll(),i=t;n.each((function(){let e=$(this);i-=e.height(),i-=parseInt(e.css("margin-top")),i-=7})),i<0&&(i=0),e.css("margin-top",i+"px")}showInlineAmendment(e){this.activeAmendmentId&&this.hideInlineAmendment(this.activeAmendmentId),this.$element.find("> .textOrig").addClass("hidden"),this.$element.find("> .textAmendment").addClass("hidden"),this.$element.find("> .textAmendment.amendment"+e).removeClass("hidden"),this.$element.find(".bookmarks .amendment"+e).find("a").addClass("active"),this.activeAmendmentId=e}hideInlineAmendment(e){this.$element.find("> .textOrig").removeClass("hidden"),this.$element.find("> .textAmendment").addClass("hidden"),this.$element.find(".bookmarks .amendment"+e).find("a").removeClass("active"),this.activeAmendmentId=null}toggleInlineAmendmentBehavior(e){const t=e.find("a"),n=t.data("id");$("html").hasClass("touchevents")?t.on("click",(e=>{e.preventDefault(),this.$element.find("> .textAmendment.amendment"+n).hasClass("hidden")?this.showInlineAmendment(n):this.hideInlineAmendment(n)})):e.on("mouseover",(()=>{this.showInlineAmendment(n)})).on("mouseout",(()=>{this.hideInlineAmendment(n)}))}}new class{constructor(){new MotionInitiatorShow,new n.LineNumberHighlighting;let e=$(".motionTextHolder .paragraph");e.find(".comment .shower").on("click",this.showComment.bind(this)),e.find(".comment .hider").on("click",this.hideComment.bind(this)),e.filter(".commentsOpened").find(".comment .shower").trigger("click"),e.filter(":not(.commentsOpened)").find(".comment .hider").trigger("click"),e.each(((e,t)=>{new i($(t))})),$(".tagAdderHolder").on("click",(function(e){e.preventDefault(),$(this).addClass("hidden"),$("#tagAdderForm").removeClass("hidden")}));let t=location.hash.split("#comm");2==t.length&&$("#comment"+t[1]).scrollintoview({top_offset:-100}),$("form.delLink").on("submit",this.delSubmit.bind(this)),$(".share_buttons a").on("click",this.shareLinkClicked.bind(this)),this.markMovedParagraphs(),this.initPrivateComments(),this.initCmdEnterSubmit()}markMovedParagraphs(){$(".motionTextHolder .moved .moved").removeClass("moved"),$(".motionTextHolder .moved").each((function(){let e,t=$(this),n=t.data("moving-partner-paragraph"),i=t.parents(".paragraph").first().attr("id").split("_")[1],r=$("#section_"+i+"_"+n).find(".lineNumber").first().data("line-number");e=t.hasClass("inserted")?__t("std","moved_paragraph_from_line"):__t("std","moved_paragraph_to_line"),e=e.replace(/##LINE##/,r).replace(/##PARA##/,n+1),"LI"===t[0].nodeName&&(t=t.parent());let a=$('<div class="movedParagraphHint"></div>');a.text(e),a.insertBefore(t)}))}initPrivateComments(){$(".privateParagraph, .privateNote").length>0&&$(".privateParagraphNoteOpener").removeClass("hidden"),$(".privateNoteOpener").on("click",(e=>{e.preventDefault(),$(".privateNoteOpener").remove(),$(".motionData .privateNotes").removeClass("hidden"),$(".motionData .privateNotes textarea").trigger("focus"),$(".privateParagraphNoteOpener").removeClass("hidden")})),$(".privateParagraphNoteOpener button").on("click",(e=>{$(e.currentTarget).parents(".privateParagraphNoteOpener").addClass("hidden");const t=$(e.currentTarget).parents(".privateParagraphNoteHolder").find("form");t.removeClass("hidden"),t.find("textarea").trigger("focus")})),$(".privateNotes blockquote").on("click",(()=>{$(".privateNotes blockquote").addClass("hidden"),$(".privateNotes form").removeClass("hidden"),$(".privateNotes textarea").trigger("focus")})),$(".privateParagraphNoteHolder blockquote").on("click",(e=>{const t=$(e.currentTarget).parents(".privateParagraphNoteHolder");t.find("blockquote").addClass("hidden"),t.find("form").removeClass("hidden"),t.find("textarea").trigger("focus")}))}delSubmit(e){e.preventDefault();let t=e.target;bootbox.confirm(__t("std","del_confirm"),(e=>{e&&t.submit()}))}shareLinkClicked(e){let t=$(e.currentTarget).attr("href");window.open(t,"_blank","width=600,height=460")&&e.preventDefault()}showComment(e){e.preventDefault();const t=$(e.currentTarget),n=t.parents(".paragraph").first().find(".commentHolder"),i=t.parent();t.addClass("hidden"),i.find(".hider").removeClass("hidden"),n.removeClass("hidden"),n.isOnScreen(.1,.1)||n.scrollintoview({top_offset:-100})}hideComment(e){const t=$(e.currentTarget),n=t.parent();t.addClass("hidden"),n.find(".shower").removeClass("hidden"),t.parents(".paragraph").first().find(".commentHolder").addClass("hidden"),e.preventDefault()}initCmdEnterSubmit(){$(document).on("keypress","form textarea",(e=>{if(e.originalEvent.metaKey&&13===e.originalEvent.keyCode){$(e.currentTarget).parents("form").first().find("button[type=submit]").trigger("click")}}))}}}));
//# sourceMappingURL=MotionShow.js.map
