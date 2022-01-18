define(["require","exports"],(function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.VotingAdmin=void 0;t.VotingAdmin=class{constructor(e){this.element=e[0],this.createVueWidget(),this.initVotingCreater(),$('[data-toggle="tooltip"]').tooltip()}createVueWidget(){const e=this.element.querySelector(".votingAdmin"),t=this.element.getAttribute("data-url-vote-settings"),i=this.element.getAttribute("data-vote-create"),s=JSON.parse(this.element.getAttribute("data-addable-motions")),n=this.element.getAttribute("data-url-poll"),o=this.element.getAttribute("data-voting"),r=JSON.parse(this.element.getAttribute("data-user-groups"));this.widget=new Vue({el:e,template:'<div class="adminVotings">\n                <voting-admin-widget v-for="voting in votings"\n                                     :voting="voting"\n                                     :addableMotions="addableMotions"\n                                     :alreadyAddedItems="alreadyAddedItems"\n                                     :userGroups="userGroups"\n                                     @set-status="setStatus"\n                                     @save-settings="saveSettings"\n                                     @remove-item="removeItem"\n                                     @delete-voting="deleteVoting"\n                                     @add-imotion="addIMotion"\n                                     @add-question="addQuestion"\n                                     ref="voting-admin-widget"\n                ></voting-admin-widget>\n            </div>',data:()=>({votingsJson:null,votings:null,userGroups:r,addableMotions:s,csrf:document.querySelector("head meta[name=csrf-token]").getAttribute("content"),pollingId:null}),computed:{alreadyAddedItems:function(){const e=[],t=[];return this.votings.forEach((i=>{i.items.forEach((i=>{"motion"===i.type&&e.push(i.id),"amendment"===i.type&&t.push(i.id)}))})),{motions:e,amendments:t}}},methods:{_performOperation:function(e,i){let s={_csrf:this.csrf};i&&(s=Object.assign(s,i));const n=this,o=t.replace(/VOTINGBLOCKID/,e);$.post(o,s,(function(e){void 0===e.success||e.success?n.votings=e:alert(e.message)})).catch((function(e){alert(e.responseText)}))},setVotingFromJson(e){e!==this.votingsJson&&(this.votings=JSON.parse(e),this.votingsJson=e)},setVotingFromObject(e){this.votings=e,this.votingsJson=null},setStatus(e,t,i){this._performOperation(e,{op:"update-status",status:t,organizations:i.map((e=>({id:e.id,members_present:e.members_present})))})},saveSettings(e,t,i,s,n,o,r,a){this._performOperation(e,{op:"save-settings",title:t,answerTemplate:i,majorityType:s,votePolicy:n,resultsPublic:o,votesPublic:r,assignedMotion:a})},deleteVoting(e){this._performOperation(e,{op:"delete-voting"})},createVoting:function(e,t,s,n,o,r,a,d,l,c){let u={_csrf:this.csrf,type:e,answers:t,title:s,specificQuestion:n,assignedMotion:o,majorityType:r,votePolicy:a,userGroups:d,resultsPublic:l,votesPublic:c};const g=this;$.post(i,u,(function(e){void 0===e.success||e.success?(g.votings=e.votings,window.setTimeout((()=>{$("#voting"+e.created_voting).scrollintoview({top_offset:-100})}),200)):alert(e.message)})).catch((function(e){alert(e.responseText)}))},removeItem(e,t,i){this._performOperation(e,{op:"remove-item",itemType:t,itemId:i})},addIMotion(e,t){this._performOperation(e,{op:"add-imotion",itemDefinition:t})},addQuestion(e,t){this._performOperation(e,{op:"add-question",question:t})},reloadData:function(){const e=this;$.get(n,(function(t){e.setVotingFromJson(t)}),"text").catch((function(e){console.error("Could not load voting data from backend",e)}))},startPolling:function(){const e=this;this.pollingId=window.setInterval((function(){e.reloadData()}),3e3)}},beforeDestroy(){window.clearInterval(this.pollingId)},created(){this.setVotingFromJson(o),this.startPolling()}}),window.votingAdminWidget=this.widget}initPolicyWidget(){const e=$(this.element),t=e.find(".userGroupSelect");t.find("select").selectize({});const i=e.find(".policySelect");i.on("change",(()=>{6===parseInt(i.val(),10)?t.removeClass("hidden"):t.addClass("hidden")})).trigger("change")}initVotingCreater(){const e=this.element.querySelector(".createVotingOpener"),t=this.element.querySelector(".createVotingHolder"),i=this.element.querySelector(".specificQuestion"),s=this.element.querySelector(".majorityTypeSettings");e.addEventListener("click",(()=>{t.classList.remove("hidden"),e.classList.add("hidden")}));const n=(e,i)=>{let s=i;return t.querySelectorAll(e).forEach((e=>{const t=e;t.checked&&(s=t.value)})),s},o=()=>{"question"===n(".votingType input","question")?i.classList.remove("hidden"):i.classList.add("hidden")};t.querySelectorAll(".votingType input").forEach((e=>{e.addEventListener("change",o)})),o();const r=()=>{"2"===n(".answerTemplate input","0")?s.classList.add("hidden"):s.classList.remove("hidden")};t.querySelectorAll(".answerTemplate input").forEach((e=>{e.addEventListener("change",r)})),r(),this.initPolicyWidget(),t.querySelector("form").addEventListener("submit",(i=>{i.stopPropagation(),i.preventDefault();const s=n(".votingType input","question"),o=parseInt(n(".answerTemplate input","0"),10),r=t.querySelector(".settingsTitle"),a=t.querySelector(".settingsQuestion"),d=t.querySelector(".settingsAssignedMotion"),l=parseInt(n(".majorityTypeSettings input","1"),10),c=parseInt(n(".resultsPublicSettings input","1"),10),u=parseInt(n(".votesPublicSettings input","0"),10),g=parseInt(t.querySelector(".policySelect").value,10);let p;p=6===g?t.querySelector(".userGroupSelectList").selectize.items.map((e=>parseInt(e,10))):null,console.log("Policy",g,p),this.widget.createVoting(s,o,r.value,a.value,d.value,l,g,p,c,u),t.classList.add("hidden"),e.classList.remove("hidden")}))}}}));
//# sourceMappingURL=VotingAdmin.js.map
