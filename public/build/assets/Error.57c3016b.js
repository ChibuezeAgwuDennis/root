import{_ as t,o as s,c as n,d as r,t as o}from"./app.81d063b9.js";const a={props:{status:{type:Number,required:!0}},computed:{title(){return{503:"503: Service Unavailable",500:"500: Server Error",404:"404: Page Not Found",403:"403: Forbidden"}[this.status]},description(){return{503:"Sorry, we are doing some maintenance. Please check back soon.",500:"Whoops, something went wrong on our servers.",404:"Sorry, the page you are looking for could not be found.",403:"Sorry, you are forbidden from accessing this page."}[this.status]}}};function c(i,u,d,l,p,e){return s(),n("div",null,[r("h1",null,o(e.title),1),r("div",null,o(e.description),1)])}const m=t(a,[["render",c]]);export{m as default};
