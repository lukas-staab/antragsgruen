define(["require","exports"],(function(e,s){"use strict";Object.defineProperty(s,"__esModule",{value:!0}),s.InitDb=void 0;s.InitDb=class{constructor(e){this.$form=e;let s=e.find(".testDBcaller");this.dbTestUrl=s.data("url"),this.dbTestUrlNotSoPretty=s.data("url-not-so-pretty"),$("#sqlPassword").on("keyup",(function(){$("#sqlPasswordNone").prop("checked",!1)})),$("#sqlPasswordNone").on("change",(function(){$(this).prop("checked")&&$("#sqlPassword").val("").attr("placeholder","")})),s.click(this.testDb.bind(this)),""==$("#sqlHost").val()&&""==$("#sqlPassword").val()||s.click(),$("#language").on("changed.fu.selectlist",this.gotoLanguageVariant.bind(this))}gotoLanguageVariant(e,s){let t=window.location.href.split("?")[0];t+="?language="+s.value,window.location.href=t}testDbResult(e){let s=$(".testDBRpending"),t=$(".testDBsuccess"),a=$(".testDBerror"),r=$(".createTables");e.success?(t.removeClass("hidden"),e.alreadyCreated?r.addClass("alreadyCreated"):r.removeClass("alreadyCreated")):(a.removeClass("hidden"),a.find(".result").text(e.error),r.removeClass("alreadyCreated")),s.addClass("hidden")}testDb(){let e=$(".testDBRpending"),s=$(".testDBsuccess"),t=$(".testDBerror"),a=$("input[name=_csrf]").val(),r={sqlType:$("input[name=sqlType]").val(),sqlHost:$("input[name=sqlHost]").val(),sqlUsername:$("input[name=sqlUsername]").val(),sqlPassword:$("input[name=sqlPassword]").val(),sqlDB:$("input[name=sqlDB]").val(),_csrf:a};$("input[name=sqlPasswordNone]").prop("checked")&&(r.sqlPasswordNone=1),e.removeClass("hidden"),t.addClass("hidden"),s.addClass("hidden"),$.post(this.dbTestUrl,r,this.testDbResult.bind(this)).fail((e=>{404===e.status?(r.disablePrettyUrl="1",$.post(this.dbTestUrlNotSoPretty,r,(e=>{this.testDbResult(e),$("input[name=prettyUrls]").val("0")})).fail((e=>{alert("An internal error occurred: "+e.status+" / "+e.responseText)}))):alert("An internal error occurred: "+e.status+" / "+e.responseText)}))}}}));
//# sourceMappingURL=InitDb.js.map
