import{_ as f,L as p,W as k,r as l,o as t,c as o,F as _,a as m,e as i,d as s,b as d,j as g,t as c,m as v,h as L}from"./app.81d063b9.js";import{A}from"./Actions.e3e969c7.js";const w={components:{Actions:A,Link:p,Widget:k},props:{actions:{type:Array,default:()=>[]},model:{type:Object,required:!0},widgets:{type:Array,default:()=>[]},resource:{type:Object,required:!0}},layout:function(a,r){return a(this.resolveDefaultLayout(),()=>r)},methods:{destroy(){this.$inertia.delete(this.model.url,{onBefore:()=>confirm(this.__("Are you sure?"))})}}},B={key:0,class:"app-widget"},S={class:"app-operation"},C={class:"app-operation__edit"},W={class:"app-card card"},j={class:"table-responsive"},x={class:"table table--striped table--clear-border table--rounded table--sm"},D={style:{width:"10rem","text-align":"end"}},N=["innerHTML"];function T(a,r,e,V,q,u){const y=l("Widget"),b=l("Actions"),h=l("Link");return t(),o("div",null,[e.widgets.length>0?(t(),o("div",B,[(t(!0),o(_,null,m(e.widgets,n=>(t(),d(y,v({key:n.key},n),null,16))),128))])):i("",!0),s("div",S,[e.actions.length>0?(t(),d(b,{key:0,selection:[e.model.id],actions:e.actions,onSuccess:a.clearSelection},null,8,["selection","actions","onSuccess"])):i("",!0),s("div",C,[e.model.abilities.update?(t(),d(h,{key:0,class:"btn btn--sm btn--tertiary",href:`${e.model.url}/edit`},{default:g(()=>[L(c(a.__("Edit")),1)]),_:1},8,["href"])):i("",!0),e.model.abilities.delete?(t(),o("button",{key:1,type:"button",class:"btn btn--sm btn--delete",onClick:r[0]||(r[0]=(...n)=>u.destroy&&u.destroy(...n))},c(a.__("Delete")),1)):i("",!0)])]),s("div",W,[s("div",j,[s("table",x,[s("tbody",null,[(t(!0),o(_,null,m(e.model.fields,n=>(t(),o("tr",{key:n.name},[s("th",D,c(n.label),1),s("td",null,[s("div",{innerHTML:n.formatted_value},null,8,N)])]))),128))])])])])])}const H=f(w,[["render",T]]);export{H as default};
