/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

!function(root,factory){"function"==typeof define&&define.amd?define(factory):"object"==typeof exports?module.exports=factory(require,exports,module):root.Tether=factory()}(this,function(require,exports,module){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}function getActualBoundingClientRect(node){var boundingRect=node.getBoundingClientRect(),rect={};for(var k in boundingRect)rect[k]=boundingRect[k];if(node.ownerDocument!==document){var _frameElement=node.ownerDocument.defaultView.frameElement;if(_frameElement){var frameRect=getActualBoundingClientRect(_frameElement);rect.top+=frameRect.top,rect.bottom+=frameRect.top,rect.left+=frameRect.left,rect.right+=frameRect.left}}return rect}function getScrollParents(el){var position=(getComputedStyle(el)||{}).position,parents=[];if("fixed"===position)return[el];for(var parent=el;(parent=parent.parentNode)&&parent&&1===parent.nodeType;){var style=void 0;try{style=getComputedStyle(parent)}catch(err){}if(void 0===style||null===style)return parents.push(parent),parents;var _style=style,overflow=_style.overflow,overflowX=_style.overflowX,overflowY=_style.overflowY;/(auto|scroll)/.test(overflow+overflowY+overflowX)&&("absolute"!==position||["relative","absolute","fixed"].indexOf(style.position)>=0)&&parents.push(parent)}return parents.push(el.ownerDocument.body),el.ownerDocument!==document&&parents.push(el.ownerDocument.defaultView),parents}function removeUtilElements(){zeroElement&&document.body.removeChild(zeroElement),zeroElement=null}function getBounds(el){var doc=void 0;el===document?(doc=document,el=document.documentElement):doc=el.ownerDocument;var docEl=doc.documentElement,box=getActualBoundingClientRect(el),origin=getOrigin();return box.top-=origin.top,box.left-=origin.left,void 0===box.width&&(box.width=document.body.scrollWidth-box.left-box.right),void 0===box.height&&(box.height=document.body.scrollHeight-box.top-box.bottom),box.top=box.top-docEl.clientTop,box.left=box.left-docEl.clientLeft,box.right=doc.body.clientWidth-box.width-box.left,box.bottom=doc.body.clientHeight-box.height-box.top,box}function getOffsetParent(el){return el.offsetParent||document.documentElement}function getScrollBarSize(){if(_scrollBarSize)return _scrollBarSize;var inner=document.createElement("div");inner.style.width="100%",inner.style.height="200px";var outer=document.createElement("div");extend(outer.style,{position:"absolute",top:0,left:0,pointerEvents:"none",visibility:"hidden",width:"200px",height:"150px",overflow:"hidden"}),outer.appendChild(inner),document.body.appendChild(outer);var widthContained=inner.offsetWidth;outer.style.overflow="scroll";var widthScroll=inner.offsetWidth;widthContained===widthScroll&&(widthScroll=outer.clientWidth),document.body.removeChild(outer);var width=widthContained-widthScroll;return _scrollBarSize={width:width,height:width}}function extend(){var out=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],args=[];return Array.prototype.push.apply(args,arguments),args.slice(1).forEach(function(obj){if(obj)for(var key in obj)({}).hasOwnProperty.call(obj,key)&&(out[key]=obj[key])}),out}function removeClass(el,name){if(void 0!==el.classList)name.split(" ").forEach(function(cls){cls.trim()&&el.classList.remove(cls)});else{var regex=new RegExp("(^| )"+name.split(" ").join("|")+"( |$)","gi"),className=getClassName(el).replace(regex," ");setClassName(el,className)}}function addClass(el,name){if(void 0!==el.classList)name.split(" ").forEach(function(cls){cls.trim()&&el.classList.add(cls)});else{removeClass(el,name);var cls=getClassName(el)+" "+name;setClassName(el,cls)}}function hasClass(el,name){if(void 0!==el.classList)return el.classList.contains(name);var className=getClassName(el);return new RegExp("(^| )"+name+"( |$)","gi").test(className)}function getClassName(el){return el.className instanceof el.ownerDocument.defaultView.SVGAnimatedString?el.className.baseVal:el.className}function setClassName(el,className){el.setAttribute("class",className)}function updateClasses(el,add,all){all.forEach(function(cls){-1===add.indexOf(cls)&&hasClass(el,cls)&&removeClass(el,cls)}),add.forEach(function(cls){hasClass(el,cls)||addClass(el,cls)})}function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}function _inherits(subClass,superClass){if("function"!=typeof superClass&&null!==superClass)throw new TypeError("Super expression must either be null or a function, not "+typeof superClass);subClass.prototype=Object.create(superClass&&superClass.prototype,{constructor:{value:subClass,enumerable:!1,writable:!0,configurable:!0}}),superClass&&(Object.setPrototypeOf?Object.setPrototypeOf(subClass,superClass):subClass.__proto__=superClass)}function within(a,b){var diff=arguments.length<=2||void 0===arguments[2]?1:arguments[2];return a+diff>=b&&b>=a-diff}function now(){return"undefined"!=typeof performance&&void 0!==performance.now?performance.now():+new Date}function addOffset(){for(var out={top:0,left:0},_len=arguments.length,offsets=Array(_len),_key=0;_key<_len;_key++)offsets[_key]=arguments[_key];return offsets.forEach(function(_ref){var top=_ref.top,left=_ref.left;"string"==typeof top&&(top=parseFloat(top,10)),"string"==typeof left&&(left=parseFloat(left,10)),out.top+=top,out.left+=left}),out}function offsetToPx(offset,size){return"string"==typeof offset.left&&-1!==offset.left.indexOf("%")&&(offset.left=parseFloat(offset.left,10)/100*size.width),"string"==typeof offset.top&&-1!==offset.top.indexOf("%")&&(offset.top=parseFloat(offset.top,10)/100*size.height),offset}function getBoundingRect(tether,to){return"scrollParent"===to?to=tether.scrollParents[0]:"window"===to&&(to=[pageXOffset,pageYOffset,innerWidth+pageXOffset,innerHeight+pageYOffset]),to===document&&(to=to.documentElement),void 0!==to.nodeType&&function(){var node=to,size=getBounds(to),pos=size,style=getComputedStyle(to);if(to=[pos.left,pos.top,size.width+pos.left,size.height+pos.top],node.ownerDocument!==document){var win=node.ownerDocument.defaultView;to[0]+=win.pageXOffset,to[1]+=win.pageYOffset,to[2]+=win.pageXOffset,to[3]+=win.pageYOffset}BOUNDS_FORMAT.forEach(function(side,i){"Top"===(side=side[0].toUpperCase()+side.substr(1))||"Left"===side?to[i]+=parseFloat(style["border"+side+"Width"]):to[i]-=parseFloat(style["border"+side+"Width"])})}(),to}var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),TetherBase=void 0;void 0===TetherBase&&(TetherBase={modules:[]});var zeroElement=null,uniqueId=function(){var id=0;return function(){return++id}}(),zeroPosCache={},getOrigin=function(){var node=zeroElement;node&&document.body.contains(node)||((node=document.createElement("div")).setAttribute("data-tether-id",uniqueId()),extend(node.style,{top:0,left:0,position:"absolute"}),document.body.appendChild(node),zeroElement=node);var id=node.getAttribute("data-tether-id");return void 0===zeroPosCache[id]&&(zeroPosCache[id]=getActualBoundingClientRect(node),defer(function(){delete zeroPosCache[id]})),zeroPosCache[id]},_scrollBarSize=null,deferred=[],defer=function(fn){deferred.push(fn)},flush=function(){for(var fn=void 0;fn=deferred.pop();)fn()},Evented=function(){function Evented(){_classCallCheck(this,Evented)}return _createClass(Evented,[{key:"on",value:function(event,handler,ctx){var once=!(arguments.length<=3||void 0===arguments[3])&&arguments[3];void 0===this.bindings&&(this.bindings={}),void 0===this.bindings[event]&&(this.bindings[event]=[]),this.bindings[event].push({handler:handler,ctx:ctx,once:once})}},{key:"once",value:function(event,handler,ctx){this.on(event,handler,ctx,!0)}},{key:"off",value:function(event,handler){if(void 0!==this.bindings&&void 0!==this.bindings[event])if(void 0===handler)delete this.bindings[event];else for(var i=0;i<this.bindings[event].length;)this.bindings[event][i].handler===handler?this.bindings[event].splice(i,1):++i}},{key:"trigger",value:function(event){if(void 0!==this.bindings&&this.bindings[event]){for(var i=0,_len=arguments.length,args=Array(_len>1?_len-1:0),_key=1;_key<_len;_key++)args[_key-1]=arguments[_key];for(;i<this.bindings[event].length;){var _bindings$event$i=this.bindings[event][i],handler=_bindings$event$i.handler,ctx=_bindings$event$i.ctx,once=_bindings$event$i.once,context=ctx;void 0===context&&(context=this),handler.apply(context,args),once?this.bindings[event].splice(i,1):++i}}}}]),Evented}();TetherBase.Utils={getActualBoundingClientRect:getActualBoundingClientRect,getScrollParents:getScrollParents,getBounds:getBounds,getOffsetParent:getOffsetParent,extend:extend,addClass:addClass,removeClass:removeClass,hasClass:hasClass,updateClasses:updateClasses,defer:defer,flush:flush,uniqueId:uniqueId,Evented:Evented,getScrollBarSize:getScrollBarSize,removeUtilElements:removeUtilElements};var _slicedToArray=function(){function sliceIterator(arr,i){var _arr=[],_n=!0,_d=!1,_e=void 0;try{for(var _s,_i=arr[Symbol.iterator]();!(_n=(_s=_i.next()).done)&&(_arr.push(_s.value),!i||_arr.length!==i);_n=!0);}catch(err){_d=!0,_e=err}finally{try{!_n&&_i.return&&_i.return()}finally{if(_d)throw _e}}return _arr}return function(arr,i){if(Array.isArray(arr))return arr;if(Symbol.iterator in Object(arr))return sliceIterator(arr,i);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),_createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),_get=function(_x6,_x7,_x8){for(var _again=!0;_again;){var object=_x6,property=_x7,receiver=_x8;_again=!1,null===object&&(object=Function.prototype);var desc=Object.getOwnPropertyDescriptor(object,property);if(void 0!==desc){if("value"in desc)return desc.value;var getter=desc.get;if(void 0===getter)return;return getter.call(receiver)}var parent=Object.getPrototypeOf(object);if(null===parent)return;_x6=parent,_x7=property,_x8=receiver,_again=!0,desc=parent=void 0}};if(void 0===TetherBase)throw new Error("You must include the utils.js file before tether.js");var _TetherBase$Utils=TetherBase.Utils,getScrollParents=_TetherBase$Utils.getScrollParents,getBounds=_TetherBase$Utils.getBounds,getOffsetParent=_TetherBase$Utils.getOffsetParent,extend=_TetherBase$Utils.extend,addClass=_TetherBase$Utils.addClass,removeClass=_TetherBase$Utils.removeClass,updateClasses=_TetherBase$Utils.updateClasses,defer=_TetherBase$Utils.defer,flush=_TetherBase$Utils.flush,getScrollBarSize=_TetherBase$Utils.getScrollBarSize,removeUtilElements=_TetherBase$Utils.removeUtilElements,transformKey=function(){if("undefined"==typeof document)return"";for(var el=document.createElement("div"),transforms=["transform","WebkitTransform","OTransform","MozTransform","msTransform"],i=0;i<transforms.length;++i){var key=transforms[i];if(void 0!==el.style[key])return key}}(),tethers=[],position=function(){tethers.forEach(function(tether){tether.position(!1)}),flush()};!function(){var lastCall=null,lastDuration=null,pendingTimeout=null,tick=function tick(){if(void 0!==lastDuration&&lastDuration>16)return lastDuration=Math.min(lastDuration-16,250),void(pendingTimeout=setTimeout(tick,250));void 0!==lastCall&&now()-lastCall<10||(null!=pendingTimeout&&(clearTimeout(pendingTimeout),pendingTimeout=null),lastCall=now(),position(),lastDuration=now()-lastCall)};"undefined"!=typeof window&&void 0!==window.addEventListener&&["resize","scroll","touchmove"].forEach(function(event){window.addEventListener(event,tick)})}();var MIRROR_LR={center:"center",left:"right",right:"left"},MIRROR_TB={middle:"middle",top:"bottom",bottom:"top"},OFFSET_MAP={top:0,left:0,middle:"50%",center:"50%",bottom:"100%",right:"100%"},autoToFixedAttachment=function(attachment,relativeToAttachment){var left=attachment.left,top=attachment.top;return"auto"===left&&(left=MIRROR_LR[relativeToAttachment.left]),"auto"===top&&(top=MIRROR_TB[relativeToAttachment.top]),{left:left,top:top}},attachmentToOffset=function(attachment){var left=attachment.left,top=attachment.top;return void 0!==OFFSET_MAP[attachment.left]&&(left=OFFSET_MAP[attachment.left]),void 0!==OFFSET_MAP[attachment.top]&&(top=OFFSET_MAP[attachment.top]),{left:left,top:top}},parseOffset=function(value){var _value$split=value.split(" "),_value$split2=_slicedToArray(_value$split,2);return{top:_value$split2[0],left:_value$split2[1]}},parseAttachment=parseOffset,TetherClass=function(_Evented){function TetherClass(options){var _this=this;_classCallCheck(this,TetherClass),_get(Object.getPrototypeOf(TetherClass.prototype),"constructor",this).call(this),this.position=this.position.bind(this),tethers.push(this),this.history=[],this.setOptions(options,!1),TetherBase.modules.forEach(function(module){void 0!==module.initialize&&module.initialize.call(_this)}),this.position()}return _inherits(TetherClass,Evented),_createClass(TetherClass,[{key:"getClass",value:function(){var key=arguments.length<=0||void 0===arguments[0]?"":arguments[0],classes=this.options.classes;return void 0!==classes&&classes[key]?this.options.classes[key]:this.options.classPrefix?this.options.classPrefix+"-"+key:key}},{key:"setOptions",value:function(options){var _this2=this,pos=arguments.length<=1||void 0===arguments[1]||arguments[1],defaults={offset:"0 0",targetOffset:"0 0",targetAttachment:"auto auto",classPrefix:"tether"};this.options=extend(defaults,options);var _options=this.options,element=_options.element,target=_options.target,targetModifier=_options.targetModifier;if(this.element=element,this.target=target,this.targetModifier=targetModifier,"viewport"===this.target?(this.target=document.body,this.targetModifier="visible"):"scroll-handle"===this.target&&(this.target=document.body,this.targetModifier="scroll-handle"),["element","target"].forEach(function(key){if(void 0===_this2[key])throw new Error("Tether Error: Both element and target must be defined");void 0!==_this2[key].jquery?_this2[key]=_this2[key][0]:"string"==typeof _this2[key]&&(_this2[key]=document.querySelector(_this2[key]))}),addClass(this.element,this.getClass("element")),!1!==this.options.addTargetClasses&&addClass(this.target,this.getClass("target")),!this.options.attachment)throw new Error("Tether Error: You must provide an attachment");this.targetAttachment=parseAttachment(this.options.targetAttachment),this.attachment=parseAttachment(this.options.attachment),this.offset=parseOffset(this.options.offset),this.targetOffset=parseOffset(this.options.targetOffset),void 0!==this.scrollParents&&this.disable(),"scroll-handle"===this.targetModifier?this.scrollParents=[this.target]:this.scrollParents=getScrollParents(this.target),!1!==this.options.enabled&&this.enable(pos)}},{key:"getTargetBounds",value:function(){if(void 0===this.targetModifier)return getBounds(this.target);if("visible"===this.targetModifier)return this.target===document.body?{top:pageYOffset,left:pageXOffset,height:innerHeight,width:innerWidth}:((out={height:(bounds=getBounds(this.target)).height,width:bounds.width,top:bounds.top,left:bounds.left}).height=Math.min(out.height,bounds.height-(pageYOffset-bounds.top)),out.height=Math.min(out.height,bounds.height-(bounds.top+bounds.height-(pageYOffset+innerHeight))),out.height=Math.min(innerHeight,out.height),out.height-=2,out.width=Math.min(out.width,bounds.width-(pageXOffset-bounds.left)),out.width=Math.min(out.width,bounds.width-(bounds.left+bounds.width-(pageXOffset+innerWidth))),out.width=Math.min(innerWidth,out.width),out.width-=2,out.top<pageYOffset&&(out.top=pageYOffset),out.left<pageXOffset&&(out.left=pageXOffset),out);if("scroll-handle"===this.targetModifier){var bounds=void 0,target=this.target;target===document.body?(target=document.documentElement,bounds={left:pageXOffset,top:pageYOffset,height:innerHeight,width:innerWidth}):bounds=getBounds(target);var style=getComputedStyle(target),scrollBottom=0;(target.scrollWidth>target.clientWidth||[style.overflow,style.overflowX].indexOf("scroll")>=0||this.target!==document.body)&&(scrollBottom=15);var height=bounds.height-parseFloat(style.borderTopWidth)-parseFloat(style.borderBottomWidth)-scrollBottom,out={width:15,height:.975*height*(height/target.scrollHeight),left:bounds.left+bounds.width-parseFloat(style.borderLeftWidth)-15},fitAdj=0;height<408&&this.target===document.body&&(fitAdj=-11e-5*Math.pow(height,2)-.00727*height+22.58),this.target!==document.body&&(out.height=Math.max(out.height,24));var scrollPercentage=this.target.scrollTop/(target.scrollHeight-height);return out.top=scrollPercentage*(height-out.height-fitAdj)+bounds.top+parseFloat(style.borderTopWidth),this.target===document.body&&(out.height=Math.max(out.height,24)),out}}},{key:"clearCache",value:function(){this._cache={}}},{key:"cache",value:function(k,getter){return void 0===this._cache&&(this._cache={}),void 0===this._cache[k]&&(this._cache[k]=getter.call(this)),this._cache[k]}},{key:"enable",value:function(){var _this3=this,pos=arguments.length<=0||void 0===arguments[0]||arguments[0];!1!==this.options.addTargetClasses&&addClass(this.target,this.getClass("enabled")),addClass(this.element,this.getClass("enabled")),this.enabled=!0,this.scrollParents.forEach(function(parent){parent!==_this3.target.ownerDocument&&parent.addEventListener("scroll",_this3.position)}),pos&&this.position()}},{key:"disable",value:function(){var _this4=this;removeClass(this.target,this.getClass("enabled")),removeClass(this.element,this.getClass("enabled")),this.enabled=!1,void 0!==this.scrollParents&&this.scrollParents.forEach(function(parent){parent.removeEventListener("scroll",_this4.position)})}},{key:"destroy",value:function(){var _this5=this;this.disable(),tethers.forEach(function(tether,i){tether===_this5&&tethers.splice(i,1)}),0===tethers.length&&removeUtilElements()}},{key:"updateAttachClasses",value:function(elementAttach,targetAttach){var _this6=this;elementAttach=elementAttach||this.attachment,targetAttach=targetAttach||this.targetAttachment;var sides=["left","top","bottom","right","middle","center"];void 0!==this._addAttachClasses&&this._addAttachClasses.length&&this._addAttachClasses.splice(0,this._addAttachClasses.length),void 0===this._addAttachClasses&&(this._addAttachClasses=[]);var add=this._addAttachClasses;elementAttach.top&&add.push(this.getClass("element-attached")+"-"+elementAttach.top),elementAttach.left&&add.push(this.getClass("element-attached")+"-"+elementAttach.left),targetAttach.top&&add.push(this.getClass("target-attached")+"-"+targetAttach.top),targetAttach.left&&add.push(this.getClass("target-attached")+"-"+targetAttach.left);var all=[];sides.forEach(function(side){all.push(_this6.getClass("element-attached")+"-"+side),all.push(_this6.getClass("target-attached")+"-"+side)}),defer(function(){void 0!==_this6._addAttachClasses&&(updateClasses(_this6.element,_this6._addAttachClasses,all),!1!==_this6.options.addTargetClasses&&updateClasses(_this6.target,_this6._addAttachClasses,all),delete _this6._addAttachClasses)})}},{key:"position",value:function(){var _this7=this,flushChanges=arguments.length<=0||void 0===arguments[0]||arguments[0];if(this.enabled){this.clearCache();var targetAttachment=autoToFixedAttachment(this.targetAttachment,this.attachment);this.updateAttachClasses(this.attachment,targetAttachment);var elementPos=this.cache("element-bounds",function(){return getBounds(_this7.element)}),width=elementPos.width,height=elementPos.height;if(0===width&&0===height&&void 0!==this.lastSize){var _lastSize=this.lastSize;width=_lastSize.width,height=_lastSize.height}else this.lastSize={width:width,height:height};var targetPos=this.cache("target-bounds",function(){return _this7.getTargetBounds()}),targetSize=targetPos,offset=offsetToPx(attachmentToOffset(this.attachment),{width:width,height:height}),targetOffset=offsetToPx(attachmentToOffset(targetAttachment),targetSize),manualOffset=offsetToPx(this.offset,{width:width,height:height}),manualTargetOffset=offsetToPx(this.targetOffset,targetSize);offset=addOffset(offset,manualOffset),targetOffset=addOffset(targetOffset,manualTargetOffset);for(var left=targetPos.left+targetOffset.left-offset.left,top=targetPos.top+targetOffset.top-offset.top,i=0;i<TetherBase.modules.length;++i){var ret=TetherBase.modules[i].position.call(this,{left:left,top:top,targetAttachment:targetAttachment,targetPos:targetPos,elementPos:elementPos,offset:offset,targetOffset:targetOffset,manualOffset:manualOffset,manualTargetOffset:manualTargetOffset,scrollbarSize:scrollbarSize,attachment:this.attachment});if(!1===ret)return!1;void 0!==ret&&"object"==typeof ret&&(top=ret.top,left=ret.left)}var next={page:{top:top,left:left},viewport:{top:top-pageYOffset,bottom:pageYOffset-top-height+innerHeight,left:left-pageXOffset,right:pageXOffset-left-width+innerWidth}},doc=this.target.ownerDocument,win=doc.defaultView,scrollbarSize=void 0;return win.innerHeight>doc.documentElement.clientHeight&&(scrollbarSize=this.cache("scrollbar-size",getScrollBarSize),next.viewport.bottom-=scrollbarSize.height),win.innerWidth>doc.documentElement.clientWidth&&(scrollbarSize=this.cache("scrollbar-size",getScrollBarSize),next.viewport.right-=scrollbarSize.width),-1!==["","static"].indexOf(doc.body.style.position)&&-1!==["","static"].indexOf(doc.body.parentElement.style.position)||(next.page.bottom=doc.body.scrollHeight-top-height,next.page.right=doc.body.scrollWidth-left-width),void 0!==this.options.optimizations&&!1!==this.options.optimizations.moveElement&&void 0===this.targetModifier&&function(){var offsetParent=_this7.cache("target-offsetparent",function(){return getOffsetParent(_this7.target)}),offsetPosition=_this7.cache("target-offsetparent-bounds",function(){return getBounds(offsetParent)}),offsetParentStyle=getComputedStyle(offsetParent),offsetParentSize=offsetPosition,offsetBorder={};if(["Top","Left","Bottom","Right"].forEach(function(side){offsetBorder[side.toLowerCase()]=parseFloat(offsetParentStyle["border"+side+"Width"])}),offsetPosition.right=doc.body.scrollWidth-offsetPosition.left-offsetParentSize.width+offsetBorder.right,offsetPosition.bottom=doc.body.scrollHeight-offsetPosition.top-offsetParentSize.height+offsetBorder.bottom,next.page.top>=offsetPosition.top+offsetBorder.top&&next.page.bottom>=offsetPosition.bottom&&next.page.left>=offsetPosition.left+offsetBorder.left&&next.page.right>=offsetPosition.right){var scrollTop=offsetParent.scrollTop,scrollLeft=offsetParent.scrollLeft;next.offset={top:next.page.top-offsetPosition.top+scrollTop-offsetBorder.top,left:next.page.left-offsetPosition.left+scrollLeft-offsetBorder.left}}}(),this.move(next),this.history.unshift(next),this.history.length>3&&this.history.pop(),flushChanges&&flush(),!0}}},{key:"move",value:function(pos){var _this8=this;if(void 0!==this.element.parentNode){var same={};for(var type in pos){same[type]={};for(var key in pos[type]){for(var found=!1,i=0;i<this.history.length;++i){var point=this.history[i];if(void 0!==point[type]&&!within(point[type][key],pos[type][key])){found=!0;break}}found||(same[type][key]=!0)}}var css={top:"",left:"",right:"",bottom:""},transcribe=function(_same,_pos){if(!1!==(void 0!==_this8.options.optimizations?_this8.options.optimizations.gpu:null)){var yPos=void 0,xPos=void 0;_same.top?(css.top=0,yPos=_pos.top):(css.bottom=0,yPos=-_pos.bottom),_same.left?(css.left=0,xPos=_pos.left):(css.right=0,xPos=-_pos.right),window.matchMedia&&(window.matchMedia("only screen and (min-resolution: 1.3dppx)").matches||window.matchMedia("only screen and (-webkit-min-device-pixel-ratio: 1.3)").matches||(xPos=Math.round(xPos),yPos=Math.round(yPos))),css[transformKey]="translateX("+xPos+"px) translateY("+yPos+"px)","msTransform"!==transformKey&&(css[transformKey]+=" translateZ(0)")}else _same.top?css.top=_pos.top+"px":css.bottom=_pos.bottom+"px",_same.left?css.left=_pos.left+"px":css.right=_pos.right+"px"},moved=!1;if((same.page.top||same.page.bottom)&&(same.page.left||same.page.right)?(css.position="absolute",transcribe(same.page,pos.page)):(same.viewport.top||same.viewport.bottom)&&(same.viewport.left||same.viewport.right)?(css.position="fixed",transcribe(same.viewport,pos.viewport)):void 0!==same.offset&&same.offset.top&&same.offset.left?function(){css.position="absolute";var offsetParent=_this8.cache("target-offsetparent",function(){return getOffsetParent(_this8.target)});getOffsetParent(_this8.element)!==offsetParent&&defer(function(){_this8.element.parentNode.removeChild(_this8.element),offsetParent.appendChild(_this8.element)}),transcribe(same.offset,pos.offset),moved=!0}():(css.position="absolute",transcribe({top:!0,left:!0},pos.page)),!moved)if(this.options.bodyElement)this.options.bodyElement.appendChild(this.element);else{for(var offsetParentIsBody=!0,currentNode=this.element.parentNode;currentNode&&1===currentNode.nodeType&&"BODY"!==currentNode.tagName;){if("static"!==getComputedStyle(currentNode).position){offsetParentIsBody=!1;break}currentNode=currentNode.parentNode}offsetParentIsBody||(this.element.parentNode.removeChild(this.element),this.element.ownerDocument.body.appendChild(this.element))}var writeCSS={},write=!1;for(var key in css){var val=css[key];this.element.style[key]!==val&&(write=!0,writeCSS[key]=val)}write&&defer(function(){extend(_this8.element.style,writeCSS),_this8.trigger("repositioned")})}}}]),TetherClass}();TetherClass.modules=[],TetherBase.position=position;var Tether=extend(TetherClass,TetherBase),_slicedToArray=function(){function sliceIterator(arr,i){var _arr=[],_n=!0,_d=!1,_e=void 0;try{for(var _s,_i=arr[Symbol.iterator]();!(_n=(_s=_i.next()).done)&&(_arr.push(_s.value),!i||_arr.length!==i);_n=!0);}catch(err){_d=!0,_e=err}finally{try{!_n&&_i.return&&_i.return()}finally{if(_d)throw _e}}return _arr}return function(arr,i){if(Array.isArray(arr))return arr;if(Symbol.iterator in Object(arr))return sliceIterator(arr,i);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),getBounds=(_TetherBase$Utils=TetherBase.Utils).getBounds,extend=_TetherBase$Utils.extend,updateClasses=_TetherBase$Utils.updateClasses,defer=_TetherBase$Utils.defer,BOUNDS_FORMAT=["left","top","right","bottom"];TetherBase.modules.push({position:function(_ref){var _this=this,top=_ref.top,left=_ref.left,targetAttachment=_ref.targetAttachment;if(!this.options.constraints)return!0;var _cache=this.cache("element-bounds",function(){return getBounds(_this.element)}),height=_cache.height,width=_cache.width;if(0===width&&0===height&&void 0!==this.lastSize){var _lastSize=this.lastSize;width=_lastSize.width,height=_lastSize.height}var targetSize=this.cache("target-bounds",function(){return _this.getTargetBounds()}),targetHeight=targetSize.height,targetWidth=targetSize.width,allClasses=[this.getClass("pinned"),this.getClass("out-of-bounds")];this.options.constraints.forEach(function(constraint){var outOfBoundsClass=constraint.outOfBoundsClass,pinnedClass=constraint.pinnedClass;outOfBoundsClass&&allClasses.push(outOfBoundsClass),pinnedClass&&allClasses.push(pinnedClass)}),allClasses.forEach(function(cls){["left","top","right","bottom"].forEach(function(side){allClasses.push(cls+"-"+side)})});var addClasses=[],tAttachment=extend({},targetAttachment),eAttachment=extend({},this.attachment);return this.options.constraints.forEach(function(constraint){var to=constraint.to,attachment=constraint.attachment,pin=constraint.pin;void 0===attachment&&(attachment="");var changeAttachX=void 0,changeAttachY=void 0;if(attachment.indexOf(" ")>=0){var _attachment$split=attachment.split(" "),_attachment$split2=_slicedToArray(_attachment$split,2);changeAttachY=_attachment$split2[0],changeAttachX=_attachment$split2[1]}else changeAttachX=changeAttachY=attachment;var bounds=getBoundingRect(_this,to);"target"!==changeAttachY&&"both"!==changeAttachY||(top<bounds[1]&&"top"===tAttachment.top&&(top+=targetHeight,tAttachment.top="bottom"),top+height>bounds[3]&&"bottom"===tAttachment.top&&(top-=targetHeight,tAttachment.top="top")),"together"===changeAttachY&&("top"===tAttachment.top&&("bottom"===eAttachment.top&&top<bounds[1]?(top+=targetHeight,tAttachment.top="bottom",top+=height,eAttachment.top="top"):"top"===eAttachment.top&&top+height>bounds[3]&&top-(height-targetHeight)>=bounds[1]&&(top-=height-targetHeight,tAttachment.top="bottom",eAttachment.top="bottom")),"bottom"===tAttachment.top&&("top"===eAttachment.top&&top+height>bounds[3]?(top-=targetHeight,tAttachment.top="top",top-=height,eAttachment.top="bottom"):"bottom"===eAttachment.top&&top<bounds[1]&&top+(2*height-targetHeight)<=bounds[3]&&(top+=height-targetHeight,tAttachment.top="top",eAttachment.top="top")),"middle"===tAttachment.top&&(top+height>bounds[3]&&"top"===eAttachment.top?(top-=height,eAttachment.top="bottom"):top<bounds[1]&&"bottom"===eAttachment.top&&(top+=height,eAttachment.top="top"))),"target"!==changeAttachX&&"both"!==changeAttachX||(left<bounds[0]&&"left"===tAttachment.left&&(left+=targetWidth,tAttachment.left="right"),left+width>bounds[2]&&"right"===tAttachment.left&&(left-=targetWidth,tAttachment.left="left")),"together"===changeAttachX&&(left<bounds[0]&&"left"===tAttachment.left?"right"===eAttachment.left?(left+=targetWidth,tAttachment.left="right",left+=width,eAttachment.left="left"):"left"===eAttachment.left&&(left+=targetWidth,tAttachment.left="right",left-=width,eAttachment.left="right"):left+width>bounds[2]&&"right"===tAttachment.left?"left"===eAttachment.left?(left-=targetWidth,tAttachment.left="left",left-=width,eAttachment.left="right"):"right"===eAttachment.left&&(left-=targetWidth,tAttachment.left="left",left+=width,eAttachment.left="left"):"center"===tAttachment.left&&(left+width>bounds[2]&&"left"===eAttachment.left?(left-=width,eAttachment.left="right"):left<bounds[0]&&"right"===eAttachment.left&&(left+=width,eAttachment.left="left"))),"element"!==changeAttachY&&"both"!==changeAttachY||(top<bounds[1]&&"bottom"===eAttachment.top&&(top+=height,eAttachment.top="top"),top+height>bounds[3]&&"top"===eAttachment.top&&(top-=height,eAttachment.top="bottom")),"element"!==changeAttachX&&"both"!==changeAttachX||(left<bounds[0]&&("right"===eAttachment.left?(left+=width,eAttachment.left="left"):"center"===eAttachment.left&&(left+=width/2,eAttachment.left="left")),left+width>bounds[2]&&("left"===eAttachment.left?(left-=width,eAttachment.left="right"):"center"===eAttachment.left&&(left-=width/2,eAttachment.left="right"))),"string"==typeof pin?pin=pin.split(",").map(function(p){return p.trim()}):!0===pin&&(pin=["top","left","right","bottom"]),pin=pin||[];var pinned=[],oob=[];top<bounds[1]&&(pin.indexOf("top")>=0?(top=bounds[1],pinned.push("top")):oob.push("top")),top+height>bounds[3]&&(pin.indexOf("bottom")>=0?(top=bounds[3]-height,pinned.push("bottom")):oob.push("bottom")),left<bounds[0]&&(pin.indexOf("left")>=0?(left=bounds[0],pinned.push("left")):oob.push("left")),left+width>bounds[2]&&(pin.indexOf("right")>=0?(left=bounds[2]-width,pinned.push("right")):oob.push("right")),pinned.length&&function(){var pinnedClass=void 0;pinnedClass=void 0!==_this.options.pinnedClass?_this.options.pinnedClass:_this.getClass("pinned"),addClasses.push(pinnedClass),pinned.forEach(function(side){addClasses.push(pinnedClass+"-"+side)})}(),oob.length&&function(){var oobClass=void 0;oobClass=void 0!==_this.options.outOfBoundsClass?_this.options.outOfBoundsClass:_this.getClass("out-of-bounds"),addClasses.push(oobClass),oob.forEach(function(side){addClasses.push(oobClass+"-"+side)})}(),(pinned.indexOf("left")>=0||pinned.indexOf("right")>=0)&&(eAttachment.left=tAttachment.left=!1),(pinned.indexOf("top")>=0||pinned.indexOf("bottom")>=0)&&(eAttachment.top=tAttachment.top=!1),tAttachment.top===targetAttachment.top&&tAttachment.left===targetAttachment.left&&eAttachment.top===_this.attachment.top&&eAttachment.left===_this.attachment.left||(_this.updateAttachClasses(eAttachment,tAttachment),_this.trigger("update",{attachment:eAttachment,targetAttachment:tAttachment}))}),defer(function(){!1!==_this.options.addTargetClasses&&updateClasses(_this.target,addClasses,allClasses),updateClasses(_this.element,addClasses,allClasses)}),{top:top,left:left}}});var getBounds=(_TetherBase$Utils=TetherBase.Utils).getBounds,updateClasses=_TetherBase$Utils.updateClasses,defer=_TetherBase$Utils.defer;TetherBase.modules.push({position:function(_ref){var _this=this,top=_ref.top,left=_ref.left,_cache=this.cache("element-bounds",function(){return getBounds(_this.element)}),height=_cache.height,width=_cache.width,targetPos=this.getTargetBounds(),bottom=top+height,right=left+width,abutted=[];top<=targetPos.bottom&&bottom>=targetPos.top&&["left","right"].forEach(function(side){var targetPosSide=targetPos[side];targetPosSide!==left&&targetPosSide!==right||abutted.push(side)}),left<=targetPos.right&&right>=targetPos.left&&["top","bottom"].forEach(function(side){var targetPosSide=targetPos[side];targetPosSide!==top&&targetPosSide!==bottom||abutted.push(side)});var allClasses=[],addClasses=[],sides=["left","top","right","bottom"];return allClasses.push(this.getClass("abutted")),sides.forEach(function(side){allClasses.push(_this.getClass("abutted")+"-"+side)}),abutted.length&&addClasses.push(this.getClass("abutted")),abutted.forEach(function(side){addClasses.push(_this.getClass("abutted")+"-"+side)}),defer(function(){!1!==_this.options.addTargetClasses&&updateClasses(_this.target,addClasses,allClasses),updateClasses(_this.element,addClasses,allClasses)}),!0}});_slicedToArray=function(){function sliceIterator(arr,i){var _arr=[],_n=!0,_d=!1,_e=void 0;try{for(var _s,_i=arr[Symbol.iterator]();!(_n=(_s=_i.next()).done)&&(_arr.push(_s.value),!i||_arr.length!==i);_n=!0);}catch(err){_d=!0,_e=err}finally{try{!_n&&_i.return&&_i.return()}finally{if(_d)throw _e}}return _arr}return function(arr,i){if(Array.isArray(arr))return arr;if(Symbol.iterator in Object(arr))return sliceIterator(arr,i);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}();return TetherBase.modules.push({position:function(_ref){var top=_ref.top,left=_ref.left;if(this.options.shift){var shift=this.options.shift;"function"==typeof this.options.shift&&(shift=this.options.shift.call(this,{top:top,left:left}));var shiftTop=void 0,shiftLeft=void 0;if("string"==typeof shift){(shift=shift.split(" "))[1]=shift[1]||shift[0];var _shift2=_slicedToArray(shift,2);shiftTop=_shift2[0],shiftLeft=_shift2[1],shiftTop=parseFloat(shiftTop,10),shiftLeft=parseFloat(shiftLeft,10)}else shiftTop=shift.top,shiftLeft=shift.left;return top+=shiftTop,left+=shiftLeft,{top:top,left:left}}}}),Tether});