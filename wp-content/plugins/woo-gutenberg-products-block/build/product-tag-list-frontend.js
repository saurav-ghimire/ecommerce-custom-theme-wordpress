(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[77],{20:function(t,e,n){"use strict";n.d(e,"a",(function(){return r})),n.d(e,"b",(function(){return c}));const r=t=>!(t=>null===t)(t)&&t instanceof Object&&t.constructor===Object;function c(t,e){return r(t)&&e in t}},211:function(t,e,n){"use strict";n.d(e,"a",(function(){return c})),n(94);var r=n(42);const c=()=>r.m>1},212:function(t,e,n){"use strict";n.d(e,"a",(function(){return o}));var r=n(28),c=n(20);const o=t=>Object(r.a)(t)?JSON.parse(t)||{}:Object(c.a)(t)?t:{}},288:function(t,e,n){"use strict";n.d(e,"a",(function(){return a}));var r=n(104),c=n(211),o=n(20),s=n(212);const a=t=>{if(!Object(c.a)())return{className:"",style:{}};const e=Object(o.a)(t)?t:{},n=Object(s.a)(e.style);return Object(r.__experimentalUseColorProps)({...e,style:n})}},294:function(t,e,n){"use strict";n.d(e,"a",(function(){return o}));var r=n(20),c=n(212);const o=t=>{const e=Object(r.a)(t)?t:{},n=Object(c.a)(e.style),o=Object(r.a)(n.typography)?n.typography:{};return{style:{fontSize:e.fontSize?`var(--wp--preset--font-size--${e.fontSize})`:o.fontSize,lineHeight:o.lineHeight,fontWeight:o.fontWeight,textTransform:o.textTransform,fontFamily:e.fontFamily}}}},390:function(t,e){},434:function(t,e,n){"use strict";n.r(e);var r=n(0),c=n(1),o=n(7),s=n.n(o),a=n(46),i=n(288),u=n(294),l=n(8),f=n(129);n(390),e.default=Object(f.withProductDataContext)(t=>{const{className:e}=t,{parentClassName:n}=Object(a.useInnerBlockLayoutContext)(),{product:o}=Object(a.useProductDataContext)(),f=Object(i.a)(t),p=Object(u.a)(t);return Object(l.isEmpty)(o.tags)?null:Object(r.createElement)("div",{className:s()(e,f.className,"wc-block-components-product-tag-list",{[n+"__product-tag-list"]:n}),style:{...f.style,...p.style}},Object(c.__)("Tags:","woo-gutenberg-products-block")," ",Object(r.createElement)("ul",null,Object.values(o.tags).map(t=>{let{name:e,link:n,slug:c}=t;return Object(r.createElement)("li",{key:"tag-list-item-"+c},Object(r.createElement)("a",{href:n},e))})))})},7:function(t,e,n){var r;!function(){"use strict";var n={}.hasOwnProperty;function c(){for(var t=[],e=0;e<arguments.length;e++){var r=arguments[e];if(r){var o=typeof r;if("string"===o||"number"===o)t.push(r);else if(Array.isArray(r)){if(r.length){var s=c.apply(null,r);s&&t.push(s)}}else if("object"===o)if(r.toString===Object.prototype.toString)for(var a in r)n.call(r,a)&&r[a]&&t.push(a);else t.push(r.toString())}}return t.join(" ")}t.exports?(c.default=c,t.exports=c):void 0===(r=function(){return c}.apply(e,[]))||(t.exports=r)}()}}]);