define(["require","exports"],(function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.Theming=void 0;class i{constructor(e){this.$row=e;const t=e.find(".uploadCol label .text");e.on("click",".imageChooserDd ul a",(i=>{i.preventDefault();const n=$(i.currentTarget).find("img").attr("src");e.find("input[type=hidden]").val(n),0===e.find(".logoPreview img").length&&e.find(".logoPreview").prepend('<img src="" alt="">'),e.find(".logoPreview img").attr("src",n).removeClass("hidden"),t.text(t.data("title")),e.find("input[type=file]").val("")})),e.find("input[type=file]").on("change",(()=>{const i=e.find("input[type=file]").val().split("\\"),n=i[i.length-1];e.find("input[type=hidden]").val(""),e.find(".logoPreview img").addClass("hidden"),t.text(n)}))}}t.Theming=class{constructor(e){this.$form=e,this.$form.find(".row_image").each(((e,t)=>{new i($(t))})),this.$form.on("click",".btnResetTheme",(e=>{e.preventDefault();const t={title:$(e.currentTarget).data("confirm-title"),message:$(e.currentTarget).data("confirm-message"),inputType:"radio",inputOptions:[{text:$(e.currentTarget).data("name-classic"),value:"layout-classic"},{text:$(e.currentTarget).data("name-dbjr"),value:"layout-dbjr"}],callback:e=>{if(e){const t=$('<input type="hidden" name="defaults" value="1">').attr("value",e);this.$form.append('<input type="hidden" name="resetTheme" value="1">'),this.$form.append(t),this.$form.trigger("submit")}}};bootbox.prompt(t)}))}}}));
//# sourceMappingURL=Theming.js.map
