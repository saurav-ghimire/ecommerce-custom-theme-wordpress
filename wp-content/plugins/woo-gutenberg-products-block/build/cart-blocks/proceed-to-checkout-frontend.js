(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[34],{289:function(e,t,c){"use strict";var n=c(13),o=c.n(n),s=c(0),r=c(92),a=c(7),i=c.n(a),u=c(141);c(290),t.a=e=>{let{className:t,showSpinner:c=!1,children:n,variant:a="contained",...b}=e;const l=i()("wc-block-components-button",t,a,{"wc-block-components-button--loading":c});return Object(s.createElement)(r.a,o()({className:l},b),c&&Object(s.createElement)(u.a,null),Object(s.createElement)("span",{className:"wc-block-components-button__text"},n))}},290:function(e,t){},407:function(e,t,c){"use strict";(function(e){var n=c(0),o=c(1),s=c(7),r=c.n(s),a=c(289),i=c(42),u=c(446),b=c(2),l=c(5),d=c(3);c(409),t.a=t=>{let{checkoutPageId:c,className:s}=t;const m=Object(b.getSetting)("page-"+c,!1),f=Object(l.useSelect)(e=>e(d.CHECKOUT_STORE_KEY).isCalculating()),[w,p]=Object(u.a)(),[v,O]=Object(n.useState)(!1);Object(n.useEffect)(()=>{if("function"!=typeof e.addEventListener||"function"!=typeof e.removeEventListener)return;const t=()=>{O(!1)};return e.addEventListener("pageshow",t),()=>{e.removeEventListener("pageshow",t)}},[]);const j=Object(n.createElement)(a.a,{className:"wc-block-cart__submit-button",href:m||i.d,disabled:f,onClick:()=>O(!0),showSpinner:v},Object(o.__)("Proceed to Checkout","woo-gutenberg-products-block"));return Object(n.createElement)("div",{className:r()("wc-block-cart__submit",s)},w,Object(n.createElement)("div",{className:"wc-block-cart__submit-container"},j),"below"===p&&Object(n.createElement)("div",{className:"wc-block-cart__submit-container wc-block-cart__submit-container--sticky"},j))}}).call(this,c(408))},408:function(e,t){var c;c=function(){return this}();try{c=c||new Function("return this")()}catch(e){"object"==typeof window&&(c=window)}e.exports=c},409:function(e,t){},446:function(e,t,c){"use strict";c.d(t,"a",(function(){return s}));var n=c(0);const o={bottom:0,left:0,opacity:0,pointerEvents:"none",position:"absolute",right:0,top:0,zIndex:-1},s=()=>{const[e,t]=Object(n.useState)(""),c=Object(n.useRef)(null),s=Object(n.useRef)(new IntersectionObserver(e=>{e[0].isIntersecting?t("visible"):t(e[0].boundingClientRect.top>0?"below":"above")},{threshold:1}));return Object(n.useLayoutEffect)(()=>{const e=c.current,t=s.current;return e&&t.observe(e),()=>{t.unobserve(e)}},[]),[Object(n.createElement)("div",{"aria-hidden":!0,ref:c,style:o}),e]}},479:function(e,t,c){"use strict";c.r(t);var n=c(129),o=c(407);t.default=Object(n.withFilteredAttributes)({checkoutPageId:{type:"number",default:0},lock:{type:"object",default:{move:!0,remove:!0}}})(o.a)}}]);