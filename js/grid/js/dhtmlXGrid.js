/*
Copyright Scand LLC http://www.scbr.com
This version of Software is free for using in non-commercial applications. For commercial use please contact info@scbr.com to obtain license
*/ 
 

var globalActiveDHTMLGridObject;
String.prototype._dhx_trim = function(){
 return this.replace(/&nbsp;/g," ").replace(/(^[ \t]*)|([ \t]*$)/g,"");
}

Array.prototype._dhx_find = function(pattern){
 for(var i=0;i<this.length;i++){
 if(pattern==this[i])
 return i;
}
 return -1;
}
Array.prototype._dhx_delAt = function(ind){
 if(Number(ind)<0 || this.length==0)
 return false;
 for(var i=ind;i<this.length;i++){
 this[i]=this[i+1];
}
 this.length--;
}
Array.prototype._dhx_insertAt = function(ind,value){
 this[this.length] = null;
 for(var i=this.length-1;i>=ind;i--){
 this[i] = this[i-1]
}
 this[ind] = value
}
Array.prototype._dhx_removeAt = function(ind){
 for(var i=ind;i<this.length;i++){
 this[i] = this[i+1]
}
 this.length--;
}
 
Array.prototype._dhx_swapItems = function(ind1,ind2){
 var tmp = this[ind1];
 this[ind1] = this[ind2]
 this[ind2] = tmp;
}

 
function dhtmlXGridObject(id){
 if(id){
 if(typeof(id)=='object'){
 this.entBox = id
 this.entBox.id = "cgrid2_"+(new Date()).getTime();
}else
 this.entBox = document.getElementById(id);
}else{
 this.entBox = document.createElement("DIV");
 this.entBox.id = "cgrid2_"+(new Date()).getTime();
}
 var self = this;
 this.nm = this.entBox.nm || "grid";
 this.cell = null;
 this.row = null;
 this.editor=null;
 this.combos=new Array(0);
 this.defVal=new Array(0);
 this.rowsAr = new Array(0);
 this.rowsCol = new Array(0);
 this.selectedRows = new Array(0);
 this.rowsBuffer = new Array(new Array(0),new Array(0));
 this.loadedKidsHash = null;
 this.UserData = new Array(0)
 
 
 this.styleSheet = document.styleSheets;
 this.entBox.className = "gridbox";
 this.entBox.style.width = this.entBox.getAttribute("width")||(window.getComputedStyle?window.getComputedStyle(this.entBox,null)["width"]:(this.entBox.currentStyle?this.entBox.currentStyle["width"]:0))|| "100%";
 this.entBox.style.height = this.entBox.getAttribute("height")||(window.getComputedStyle?window.getComputedStyle(this.entBox,null)["height"]:(this.entBox.currentStyle?this.entBox.currentStyle["height"]:0))|| "100%";
 
 this.entBox.style.cursor = 'default';
 this.entBox.onselectstart = function(){return false};
 this.obj = document.createElement("TABLE");
 this.obj.cellSpacing = 0;
 this.obj.cellPadding = 0;
 this.obj.style.width = "100%";
 this.obj.style.tableLayout = "fixed";
 this.obj.className = "obj";
 
 this.hdr = document.createElement("TABLE");
 this.hdr.style.border="1px solid gray";
 this.hdr.cellSpacing = 0;
 this.hdr.cellPadding = 0;
 if(!_isOpera)
 this.hdr.style.tableLayout = "fixed";
 this.hdr.className = "hdr";
 this.hdr.width = "100%";
 this.xHdr = document.createElement("TABLE");
 this.xHdr.cellPadding = 0;
 this.xHdr.cellSpacing = 0;
 var r = this.xHdr.insertRow(0)
 var c = r.insertCell(0);
 r.insertCell(1).innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 c.appendChild(this.hdr)
 this.objBuf = document.createElement("DIV");
 this.objBuf.style.borderBottom = "1px solid white"
 this.objBuf.appendChild(this.obj);
 this.entCnt = document.createElement("TABLE");
 this.entCnt.insertRow(0).insertCell(0)
 this.entCnt.insertRow(1).insertCell(0);
 this.entCnt.cellPadding = 0;
 this.entCnt.cellSpacing = 0;
 this.entCnt.width = "100%";
 this.entCnt.height = "100%";
 this.entCnt.style.tableLayout = "fixed";
 this.objBox = document.createElement("DIV");
 this.objBox.style.width = "100%";
 this.objBox.style.height = this.entBox.style.height;
 this.objBox.style.overflow = "auto";
 this.objBox.style.position = "relative";
 this.objBox.appendChild(this.objBuf);
 this.objBox.className = "objbox";

 this.hdrBox = document.createElement("DIV");
 this.hdrBox.style.width = "100%"
 if((_isOpera)||((_isMacOS)&&(_isFF)))this.hdrSizeA=25;else this.hdrSizeA=100;

 this.hdrBox.style.height=this.hdrSizeA+"px";
 this.hdrBox.style.overflow =((_isOpera)||((_isMacOS)&&(_isFF)))?"hidden":"auto";
 this.hdrBox.style.position = "relative";
 this.hdrBox.appendChild(this.xHdr);
 this.sortImg = document.createElement("IMG")
 this.hdrBox.insertBefore(this.sortImg,this.xHdr)
 this.entCnt.rows[0].cells[0].appendChild(this.hdrBox);
 this.entCnt.rows[1].cells[0].appendChild(this.objBox);
 
 this.entBox.appendChild(this.entCnt);
 
 this.entBox.grid = this;
 this.objBox.grid = this;
 this.hdrBox.grid = this;
 this.obj.grid = this;
 this.hdr.grid = this;
 
 this.cellWidthPX = new Array(0);
 this.cellWidthPC = new Array(0);
 this.cellWidthType = this.entBox.cellwidthtype || "px";
 
 this.delim = this.entBox.delimiter || ",";
 this.hdrLabels =(this.entBox.hdrlabels || "").split(",");
 this.columnIds =(this.entBox.columnids || "").split(",");
 this.columnColor =(this.entBox.columncolor || "").split(",");
 this.cellType =(this.entBox.cellstype || "").split(",");
 this.cellAlign =(this.entBox.cellsalign || "").split(",");
 this.initCellWidth =(this.entBox.cellswidth || "").split(",");
 this.fldSort =(this.entBox.fieldstosort || "").split(",")
 this.imgURL = this.entBox.imagesurl || "gridCfx/";
 this.isActive = false;
 this.isEditable = true;
 this.raNoState = this.entBox.ranostate || "N";
 this.chNoState = this.entBox.chnostate || "N";
 this.selBasedOn =(this.entBox.selbasedon || "cell").toLowerCase()
 this.selMultiRows = this.entBox.selmultirows || false;
 this.multiLine = this.entBox.multiline || false;
 this.noHeader = this.entBox.noheader || false;
 this.dynScroll = this.entBox.dynscroll || false;
 this.dynScrollPageSize = 0;
 this.dynScrollPos = 0;
 this.xmlFileUrl = this.entBox.xmlfileurl || "";
 this.recordsNoMore = this.entBox.infinitloading || true;;
 
 
 this.rowsBufferOutSize = 0;
 
 if(this.entBox.oncheckbox)
 this.onCheckbox = eval(this.entBox.oncheckbox);
 this.onEditCell = this.entBox.oneditcell || "void";
 this.onRowSelect = this.entBox.onrowselect || "void";
 this.onEnter = this.entBox.onenter || "void";
 
 
 this.loadXML = function(url){
 if(url.indexOf("?")!=-1)
 var s = "&";
 else
 var s = "?";
 this.xmlLoader.loadXML(url+""+s+"rowsLoaded="+this.getRowsNum()+"&lastid="+this.getRowId(this.getRowsNum()-1)+"&sn="+Date.parse(new Date()));
}
 
 this.loadXMLString = function(str){
 this.xmlLoader.loadXMLString(str);
}
 
 this.doLoadDetails = function(obj){
 var root = self.xmlLoader.getXMLTopNode("rows")
 if(root.tagName!="DIV")
 if(!self.xmlLoader.xmlDoc.nodeName){
 self.parseXML(self.xmlLoader.xmlDoc.responseXML)
}else{
 self.parseXML(self.xmlLoader.xmlDoc)
}
}
 this.xmlLoader = new dtmlXMLLoaderObject(this.doLoadDetails,window);
 this.dragger=new dhtmlDragAndDropObject();

 
 
 this.doOnScroll = function(){
 this.hdrBox.scrollLeft = this.objBox.scrollLeft;
 this.setSortImgPos()
 if(this.objBox.scrollTop+this.hdrSizeA+this.objBox.offsetHeight>this.objBox.scrollHeight){
 if(this._xml_ready && this.addRowsFromBuffer())
 this.objBox.scrollTop = this.objBox.scrollHeight -(this.hdrSizeA+1+this.objBox.offsetHeight)
}
}
 
 this.attachToObject = function(obj){
 obj.appendChild(this.entBox)
 
}
 
 this.init = function(fl){
 
 this.editStop()
 
 this.lastClicked = null;
 this.resized = null;
 this.fldSorted = null;
 this.gridWidth = 0;
 this.gridHeight = 0;
 
 this.cellWidthPX = new Array(0);
 this.cellWidthPC = new Array(0);
 if(this.hdr.rows.length>0){
 this.clearAll();
 this.hdr.rows[0].removeNode(true);
}
 if(this.cellType._dhx_find("tree")!=-1){
 this.loadedKidsHash = new Hashtable();
 this.loadedKidsHash.put("hashOfParents",new Hashtable())
}
 var hdrRow = this.hdr.insertRow(0);
 for(var i=0;i<this.hdrLabels.length;i++){
 var c=hdrRow.insertCell(i);
 c.innerHTML = this.hdrLabels[i];
 c._cellIndex=i;
}
 this.setColumnIds()
 if(this.multiLine==-1)
 this.multiLine = true;
 else
 this.multiLine = false;
 
 
 
 this.sortImg.style.position = "absolute";
 this.sortImg.style.display = "none";
 this.sortImg.src = this.imgURL+"sort_desc.gif";
 this.sortImg.defLeft = 0;
 
 
 this.entCnt.rows[0].style.display = '' 
 
 if(this.noHeader==-1){
 this.noHeader = true
 this.entCnt.rows[0].style.display = 'none';
}else{
 this.noHeader = false
}
 this.setSizes();
 
 
 if(fl)
 this.parseXML()
 this.obj.scrollTop = 0
};
 
 this.setSizes = function(fl){
 if(fl && this.gridWidth==this.entBox.offsetWidth && this.gridHeight==this.entBox.offsetHeight){
 return false
}else if(fl){
 this.gridWidth = this.entBox.offsetWidth
 this.gridHeight = this.entBox.offsetHeight
}

 if(!this.hdrBox.offsetHeight)return;
 this.entCnt.rows[0].cells[0].height = this.hdrBox.offsetHeight+"px";

 var gridWidth = parseInt(this.entBox.offsetWidth);
 var gridHeight = parseInt(this.entBox.offsetHeight);
 if(this.objBox.scrollHeight>this.objBox.offsetHeight)gridWidth-=(this._scrFix||(_isFF?19:16));

 var len = this.hdr.rows[0].cells.length
 for(var i=0;i<this.hdr.rows[0].cells.length;i++){
 if(this.cellWidthType=='px' && this.cellWidthPX.length < len){
 this.cellWidthPX[i] = this.initCellWidth[i];
}else if(this.cellWidthType=='%' && this.cellWidthPC.length < len){
 this.cellWidthPC[i] = this.initCellWidth[i];
}
 if(this.cellWidthPC.length!=0){
 this.cellWidthPX[i] = parseInt(gridWidth*this.cellWidthPC[i]/100)-(_isFF?2:0);
}
}
 this.chngCellWidth(this.rowsCol._dhx_find(this.obj.rows[0]))
 var summ = 0;
 for(var i=0;i<this.cellWidthPX.length;i++)
 summ+= parseInt(this.cellWidthPX[i])
 this.objBuf.style.width = summ+"px";
 this.objBuf.childNodes[0].style.width = summ+"px";
 if(_isOpera)this.hdr.style.width = summ+this.cellWidthPX.length*2+"px";
 
 this.doOnScroll();

 
 
 if(!this.noHeader){
 if((_isMacOS)&&(_isFF))
 var zheight=20;
 else
 var zheight=this.hdr.offsetHeight;
 this.hdr.style.border="0px solid gray";
 this.entCnt.rows[1].cells[0].childNodes[0].style.top =(zheight-this.hdrSizeA+1)+"px";
 this.entCnt.rows[1].cells[0].childNodes[0].style.height =(gridHeight - zheight-1)+"px";
}
};

 
 this.chngCellWidth = function(ind){
 if(!ind)
 var ind = 0;
 for(var i=0;i<this.cellWidthPX.length;i++){
 this.hdr.rows[0].cells[i].style.width = this.cellWidthPX[i]+"px";
 if(this.rowsCol[ind])
 this.rowsCol[ind].cells[i].style.width = this.cellWidthPX[i]+"px";
}
}
 
 this.setDelimiter = function(delim){
 this.delim = delim;
}
 
 this.setInitWidthsP = function(wp){
 this.cellWidthType = "%";
 this.initCellWidth = wp.split(this.delim.replace(/px/gi,""));
}
 
 this.setInitWidths = function(wp){
 this.cellWidthType = "px";
 this.initCellWidth = wp.split(this.delim);
 if(_isFF){
 for(var i=0;i<this.initCellWidth.length;i++)
 this.initCellWidth[i]=parseInt(this.initCellWidth[i])-2;
}
}
 
 
 this.enableMultiline = function(state){
 this.multiLine = state;
}
 
 
 this.enableMultiselect = function(state){
 this.selMultiRows = state;
}
 
 
 this.setImagePath = function(path){
 this.imgURL = path;
}
 
 
 this.changeCursorState = function(ev){
 var el = ev.target||ev.srcElement;
 
 if((el.offsetWidth -(ev.offsetX||(parseInt(this.getPosition(el,this.hdrBox))-ev.layerX)*-1))<10){
 el.style.cursor = "E-resize";
}else
 el.style.cursor = "default";
}
 
 this.startColResize = function(ev){
 this.resized = null;
 var el = ev.target||ev.srcElement;
 var x = ev.layerX||ev.x;
 
 var tabW = this.hdr.offsetWidth;
 var startW = parseInt(el.offsetWidth)
 if(el.tagName=="TD" && el.style.cursor!="default"){
 this.entBox.onmousemove = function(e){this.grid.doColResize(e||window.event,el,startW,x,tabW)}
 document.body.onmouseup = new Function("","document.getElementById('"+this.entBox.id+"').grid.stopColResize()");
}
}
 
 this.stopColResize = function(){
 this.entBox.onmousemove = "";
 document.body.onmouseup = "";
 this.setSizes();
 this.doOnScroll()
}
 
 this.doColResize = function(ev,el,startW,x,tabW){
 el.style.cursor = "E-resize";
 this.resized = el;
 var fcolW = startW+((ev.layerX||ev.x)-x);
 var wtabW = tabW+((ev.layerX||ev.x)-x)

 var gridWidth = parseInt(this.entBox.offsetWidth);
 if(this.objBox.scrollHeight>this.objBox.offsetHeight)gridWidth-=(this._scrFix||(_isFF?19:16));

 if(fcolW>10){
 el.style.width = fcolW+"px";
 if(this.rowsCol.length>0)
 
 

 this.rowsCol[this.rowsCol._dhx_find(this.obj.rows[0])].cells[el._cellIndex].style.width = fcolW+"px";
 if(this.cellWidthType=='px'){
 this.cellWidthPX[el._cellIndex]=fcolW;
}else{
 var pcWidth = Math.round(fcolW/gridWidth*100)
 this.cellWidthPC[el._cellIndex]=pcWidth;
}
 this.doOnScroll()
}

 
 this.objBuf.childNodes[0].style.width = "";


}
 
 
 
 
 this.setSortImgPos = function(ind){
 if(!ind)
 var el = this.fldSorted;
 else
 var el = this.hdr.cells[ind];
 if(el!=null){
 var pos = this.getPosition(el,this.hdrBox)
 var wdth = el.offsetWidth;
 this.sortImg.style.left = Number(pos[0]+wdth-13)+"px";
 this.sortImg.defLeft = parseInt(this.sortImg.style.left)
 this.sortImg.style.top = Number(pos[1]+5)+"px";
 this.sortImg.style.display = "inline";
 this.sortImg.style.left = this.sortImg.defLeft+"px";
}
}
 
 this.parseXML = function(xml,startIndex){
 this._xml_ready=true;
 var pid=null;
 var zpid=null;
 if(!xml)
 try{
 var xmlDoc = eval(this.entBox.id+"_xml").XMLDocument;
}catch(er){

 var xmlDoc = this.loadXML(this.xmlFileUrl)
}
 else{
 if(typeof(xml)=="object"){
 var xmlDoc = xml;
}else{
 if(xml.indexOf(".")!=-1){
 if(this.xmlFileUrl=="")
 this.xmlFileUrl = xml
 var xmlDoc = this.loadXML(xml)
 return;

}else
 var xmlDoc = eval(xml).XMLDocument;
}
}

 
 var rowsCol = this.xmlLoader.doXPath("//rows/row",xmlDoc);

 if(rowsCol.length==0){
 this.recordsNoMore = true;
 var pid=0;
}
 else{
 pid=(rowsCol[0].parentNode.getAttribute("parent")||"0");
 zpid=this.getRowById(pid);
 if(zpid)zpid._xml_await=false;
 else pid=0;
 startIndex=this.getRowIndex(pid)+1;
}

 var ar = new Array();
 var idAr = new Array();
 
 var gudCol = this.xmlLoader.doXPath("//rows/userdata",xmlDoc);
 if(gudCol.length>0){
 this.UserData["gridglobaluserdata"] = new Hashtable();
 for(var j=0;j<gudCol.length;j++){
 this.UserData["gridglobaluserdata"].put(gudCol[j].getAttribute("name"),gudCol[j].firstChild?gudCol[j].firstChild.data:"");
}
}
 

 this._innerParse(rowsCol,startIndex,this.cellType._dhx_find("tree"),pid);

 if(zpid)this.expandKids(zpid);

 if(this.dynScroll && this.dynScroll!='false'){
 
 this.doDynScroll()
}

 var oCol = this.xmlLoader.doXPath("//row[@open]",xmlDoc);
 for(var i=0;i<oCol.length;i++)
 this.openItem(oCol[i].getAttribute("id"));


 this.setSizes();
 this._startXMLLoading=false;
}

 this._innerParse=function(rowsCol,startIndex,tree,pId){
 var r=null;

 for(var i=0;i<rowsCol.length;i++){
 if(i<=this.rowsBufferOutSize || this.rowsBufferOutSize==0){
 var rId = rowsCol[i].getAttribute("id")
 var xstyle = rowsCol[i].getAttribute("style");


 
 var udCol = this.xmlLoader.doXPath("./userdata",rowsCol[i]);
 if(udCol.length>0){
 this.UserData[rId] = new Hashtable();
 for(var j=0;j<udCol.length;j++){
 this.UserData[rId].put(udCol[j].getAttribute("name"),udCol[j].firstChild?udCol[j].firstChild.data:"");
}
}

 var cellsCol = this.xmlLoader.doXPath("./cell",rowsCol[i]);
 var strAr = new Array(0);

 for(var j=0;j<cellsCol.length;j++){
 if(j!=tree)
 strAr[strAr.length] = cellsCol[j].firstChild?cellsCol[j].firstChild.data:"";
 else
 strAr[strAr.length] = pId+"^"+(cellsCol[j].firstChild?cellsCol[j].firstChild.data:"")+"^"+(rowsCol[i].getAttribute("xmlkids")?"1":"0")+"^"+(cellsCol[j].getAttribute("image")||"leaf.gif");
}

 this._parsing_=true;
 if(startIndex){
 r = this._addRow(rId,strAr,startIndex)
 startIndex++;
}else{
 r = this._addRow(rId,strAr)
}
 this._parsing_=false;

 
 if(rowsCol[i].getAttribute("selected")==true){
 this.setSelectedRow(rId,false,false,rowsCol[i].getAttribute("call")==true)
}
 
 if(rowsCol[i].getAttribute("expand")=="1"){
 r.expand = "";
}
}else{
 var len = this.rowsBuffer[0].length
 this.rowsBuffer[1][len] = rowsCol[i]
 this.rowsBuffer[0][len] = rowsCol[i].getAttribute("id")
 
}


 if(tree!=-1){
 var rowsCol2 = this.xmlLoader.doXPath("./row",rowsCol[i]);
 if(rowsCol2.length!=0)
 startIndex=this._innerParse(rowsCol2,startIndex,tree,rId);
}

 if(xstyle)this.setRowTextStyle(rId,xstyle);
}
 if((r)&&(this._checkSCL))
 for(var i=0;i<this.hdr.rows[0].cells.length;i++)
 this._checkSCL(r.childNodes[i]);

 return startIndex;
}
 
 this.setActive = function(fl){
 if(arguments.length==0)
 var fl = true;
 if(fl==true){
 
 globalActiveDHTMLGridObject = this;
 this.isActive = true;
}else{
 this.isActive = false;
}
};
 
 this._doClick = function(ev){
 var selMethod = 0;
 var el = this.getFirstParentOfType(_isIE?ev.srcElement:ev.target,"TD");
 var fl = true;
 if(this.selMultiRows!=false){
 if(ev.shiftKey && this.row!=null){
 selMethod = 1;
}
 if(ev.ctrlKey){
 selMethod = 2;
}
 if(!ev.shiftKey)
 this.lastClicked = el.parentNode;
}
 this.doClick(el,fl,selMethod)
};
 
 this.doClick = function(el,fl,selMethod){
 this.setActive(true);
 if(!selMethod)
 selMethod = 0;
 if(this.cell!=null)
 this.cell.className = "";
 if(el.tagName=="TD" && this.rowsCol._dhx_find(this.rowsAr[el.parentNode.idd])!=-1){
 if(selMethod==0){
 this.clearSelection();
}else if(selMethod==1){
 var elRowIndex = this.rowsCol._dhx_find(el.parentNode)
 var lcRowIndex = this.rowsCol._dhx_find(this.lastClicked)
 if(elRowIndex>lcRowIndex){
 var strt = lcRowIndex;
 var end = elRowIndex;
}else{
 var strt = elRowIndex;
 var end = lcRowIndex;
}
 this.clearSelection();
 for(var i=0;i<this.rowsCol.length;i++){
 if(i>=strt && i<=end){
 this.rowsCol[i].className+=" rowselected";
 this.selectedRows[this.selectedRows.length] = this.rowsCol[i]
}
}
 
}else if(selMethod==2){
 if(el.parentNode.className.indexOf("rowselected")!= -1){
 el.parentNode.className=el.parentNode.className.replace("rowselected","");
 this.selectedRows._dhx_removeAt(this.selectedRows._dhx_find(el.parentNode))
 var skipRowSelection = true;
}
}
 this.editStop()
 this.cell = el;
 if(this.row != el.parentNode){
 this.row = el.parentNode;
 if(fl)
 if(typeof(this.onRowSelect)=="string")
 setTimeout(this.onRowSelect+"('"+this.row.idd+"',false);",100)
 else{
 var rid = this.row.idd
 var func = this.onRowSelect
 setTimeout(function(){func(rid,false);},100)
}
}
 if(!skipRowSelection){
 this.row.className+= " rowselected"
 if(this.selectedRows._dhx_find(this.row)==-1)
 this.selectedRows[this.selectedRows.length] = this.row;
}
 if(this.selBasedOn=="cell"){
 if(this.cell.parentNode.className.indexOf("rowselected")!=-1)
 this.cell.className = "cellselected"
}

 if(selMethod!=1)
 this.lastClicked = el.parentNode;
}
 this.isActive = true;
 this.moveToVisible(this.cell)
}
 
 this.selectCell = function(r,cInd,fl,preserve){
 if(!fl)
 fl = false;
 if(typeof(r)!="object")
 r = this.rowsCol[r]
 var c = r.childNodes[cInd];
 if(preserve)
 this.doClick(c,fl,2)
 else
 this.doClick(c,fl)
}
 
 this.moveToVisible = function(cell_obj){
 try{
 var distance = cell_obj.offsetLeft+cell_obj.offsetWidth+20;
 if(distance>(this.objBox.offsetWidth+this.objBox.scrollLeft)){
 var scrollLeft = distance - this.objBox.offsetWidth;
}else if(cell_obj.offsetLeft<this.objBox.scrollLeft){
 var scrollLeft = cell_obj.offsetLeft-5
}
 if(scrollLeft)
 this.objBox.scrollLeft = scrollLeft;
 
 var distance = cell_obj.offsetTop+cell_obj.offsetHeight+20;
 if(distance>(this.objBox.offsetHeight+this.objBox.scrollTop)){
 var scrollTop = distance - this.objBox.offsetHeight;
}else if(cell_obj.offsetTop<this.objBox.scrollTop){
 var scrollTop = cell_obj.offsetTop-5
}
 if(scrollTop)
 this.objBox.scrollTop = scrollTop;
}catch(er){
 
}
}
 
 this.editCell = function(){
 this.editStop();
 if(this.isEditable!=true)
 return false;
 var c = this.cell;
 c.className+=" editable";
 eval("this.editor = new eXcell_"+this.cellType[this.cell._cellIndex]+"(c)");
 
 if(this.editor!=null){
 if(typeof(this.onEditCell)=="string"){
 if(eval(this.onEditCell+"(0,'"+this.row.idd+"',"+this.cell._cellIndex+");")!=false){
 this.editor.edit()
 this._Opera_stop=(new Date).valueOf();
 eval(this.onEditCell+"(1,'"+this.row.idd+"',"+this.cell._cellIndex+");")
}else{
 this.editor=null;
}
}else{
 if(this.onEditCell(0,this.row.idd,this.cell._cellIndex)!=false){
 this.editor.edit()
 this.onEditCell(1,this.row.idd,this.cell._cellIndex)
}else{
 this.editor=null;
}
}
}
}
 
 this.editStop = function(){
 if(_isOpera)
 if(this._Opera_stop){
 if((this._Opera_stop*1+250)>(new Date).valueOf())return;
 this._Opera_stop=null;
}
 if(this.editor && this.editor!=null){
 this.cell.className=this.cell.className.replace("editable","");
 this.cell.wasChanged = this.editor.detach();
 if(typeof(this.onEditCell)=="string")
 eval(this.onEditCell+"(2,'"+this.row.idd+"',"+this.cell._cellIndex+");")
 else
 this.onEditCell(2,this.row.idd,this.cell._cellIndex);
}
 this.editor=null;
}
 
 this.doKey = function(ev){
 if(!ev)return true;
 if((globalActiveDHTMLGridObject)&&(this!=globalActiveDHTMLGridObject))
 return globalActiveDHTMLGridObject.doKey(ev);
 if(this.isActive==false){
 
 return false;
}
 try{
 var type = this.cellType[this.cell._cellIndex]
 
 if(ev.keyCode==13 &&(ev.ctrlKey || ev.shiftKey)){
 var rowInd = this.rowsCol._dhx_find(this.row)
 if(window.event.ctrlKey && rowInd!=this.rowsCol.length-1){
 if(this.row.rowIndex==this.obj.rows.length-1 && this.dynScroll && this.dynScroll!='false')
 this.doDynScroll("dn")
 this.selectCell(this.rowsCol[rowInd+1],this.cell._cellIndex,true);
}else if(ev.shiftKey && rowInd!=0){
 if(this.row.rowIndex==0 && this.dynScroll && this.dynScroll!='false')
 this.doDynScroll("up")
 this.selectCell(this.rowsCol[rowInd-1],this.cell._cellIndex,true);
}
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 if(ev.keyCode==13 && !ev.ctrlKey && !ev.shiftKey){
 this.editStop();
 if(typeof(this.onEnter)=="string")
 eval("window."+this.onEnter+"('"+this.row.idd+"',"+this.cell._cellIndex+")")
 else
 this.onEnter(this.row.idd,this.cell._cellIndex);
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 
 if(ev.keyCode==9 && !ev.shiftKey){
 this.editStop();
 var aind=this.cell._cellIndex+1;
 var arow=this.row;
 if(aind==this.row.childNodes.length){
 aind=0;
 arow=this.rowsCol[this.rowsCol._dhx_find(this.row)+1];
 if(!arow){
 aind=this.row.childNodes.length-1;
 return true;}
}
 this.selectCell(arow||this.row,aind);
 this.editCell()
 _isIE?ev.returnValue=false:ev.preventDefault();
}else if(ev.keyCode==9 && ev.shiftKey){
 this.editStop();
 var aind=this.cell._cellIndex-1;
 var arow=this.row;
 if(aind<0)
{
 aind=this.row.childNodes.length-1;
 arow=this.rowsCol[this.rowsCol._dhx_find(this.row)-1];
 if(!arow){aind=0;
 return true;}
}
 this.selectCell(arow||this.row,aind);
 this.editCell()
 _isIE?ev.returnValue=false:ev.preventDefault();
}
 
 if(ev.keyCode==40 || ev.keyCode==38){

 if(this.editor && this.editor.combo){
 if(ev.keyCode==40)this.editor.shiftNext();
 if(ev.keyCode==38)this.editor.shiftPrev();
 return true;
}
 else{
 var rowInd = this.row.rowIndex;
 if(ev.keyCode==38 && rowInd!=0){
 if(this.row.rowIndex==0 && this.dynScroll && this.dynScroll!='false')
 this.doDynScroll("up")
 this.selectCell(this.obj.rows[rowInd-1],this.cell._cellIndex,true);
}else if(ev.keyCode==40 && rowInd!=this.rowsCol.length-1){
 if(this.row.rowIndex==this.obj.rows.length-1 && this.dynScroll && this.dynScroll!='false')
 this.doDynScroll("dn")
 this.selectCell(this.obj.rows[rowInd+1],this.cell._cellIndex,true);
}
}
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 
 if(ev.keyCode==113){
 this.editCell();
 return false;
}
 
 if(ev.keyCode==32){
 var c = this.cell
 eval("var ed = new eXcell_"+this.cellType[c._cellIndex]+"(c)");
 
 if(ed.changeState()!=false)
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 
 if(ev.keyCode==27 && this.oe!=false){
 this.editStop();
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 
 if(ev.keyCode==33 || ev.keyCode==34){
 if(ev.keyCode==33)
 this.doDynScroll("up")
 else
 this.doDynScroll("dn")
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 
 if(!this.editor)
{
 if(ev.keyCode==37 && this.cellType._dhx_find("tree")!=-1){
 this.collapseKids(this.row)
 isIE()?ev.returnValue=false:ev.preventDefault();
}
 if(ev.keyCode==39 && this.cellType._dhx_find("tree")!=-1){
 this.expandKids(this.row)
 isIE()?ev.returnValue=false:ev.preventDefault();
}
}
 return true;
}catch(er){return true;}


}
 
 this.getRow = function(cell){
 if(!cell)
 cell = window.event.srcElement;
 if(cell.tagName!='TD')
 cell = cell.parentElement;
 r = cell.parentElement;
 if(this.cellType[cell._cellIndex]=='lk')
 eval(this.onLink+"('"+this.getRowId(r.rowIndex)+"',"+cell._cellIndex+")");
 this.selectCell(r,cell._cellIndex,true)
}
 
 this.selectRow = function(r,fl,preserve){
 if(typeof(r)!='object')
 r = this.rowsCol[r]
 this.selectCell(r,0,fl,preserve)
};
 
 this.sortRows = function(col,type,order){
 
 if(this.cellType._dhx_find("tree")!=-1){
 return this.sortTreeRows(col,type,order)
}
 if(type=='str'){
 this.rowsCol.sort(function(a,b){
 var cA = a.childNodes[col]
 var cB = b.childNodes[col]
 var type = a.grid.cellType[col];
 eval("var edA = new eXcell_"+type+"(cA)")
 eval("var edB = new eXcell_"+type+"(cB)")
 if(order=="asc")
 return edA.getValue()>edB.getValue()?1:-1
 else
 return edA.getValue()<edB.getValue()?1:-1
});
}else if(type=='int'){
 this.rowsCol.sort(function(a,b){
 var cA = a.childNodes[col]
 var cB = b.childNodes[col]
 var type = a.grid.cellType[col];
 eval("var edA = new eXcell_"+type+"(cA)")
 eval("var edB = new eXcell_"+type+"(cB)")
 var aVal = parseFloat(edA.getValue())||-99999999999999
 var bVal = parseFloat(edB.getValue())||-99999999999999
 if(order=="asc")
 return aVal-bVal
 else
 return bVal-aVal
 
});
}else if(type=='date'){
 this.rowsCol.sort(function(a,b){
 var cA = a.childNodes[col]
 var cB = b.childNodes[col]
 var type = a.grid.cellType[col];
 eval("var edA = new eXcell_"+type+"(cA)")
 eval("var edB = new eXcell_"+type+"(cB)")
 var aVal = Date.parse(new Date(edA.getValue())||new Date("01/01/1900"))
 var bVal = Date.parse(new Date(edB.getValue())||new Date("01/01/1900"))
 if(order=="asc")
 return aVal-bVal
 else
 return bVal-aVal
 
});
}
 if(this.dynScroll && this.dynScroll!='false'){
 alert("not implemented yet")
}else{
 var tb = this.obj.firstChild;
 for(var i=0;i<this.rowsCol.length;i++){
 tb.insertBefore(this.rowsCol[i],tb.childNodes[i])
 
}
}
 this.setSizes()
}
 
 
 
 this.setXMLAutoLoading = function(filePath,bufferSize){
 this.recordsNoMore = false;
 this.xmlFileUrl = filePath;
 this.rowsBufferOutSize = bufferSize||40;
}
 
 
 this.enableBuffering = function(bufferSize){
 this.rowsBufferOutSize = bufferSize||40;
}

 
 this.addRowsFromBuffer = function(){
 if(this.rowsBuffer[0].length==0){
 if(!this.recordsNoMore){
 if((this.xmlFileUrl!="")&&(!this._startXMLLoading)){
 this._startXMLLoading=true;
 this.loadXML(this.xmlFileUrl)
}
}else
 return false;
}
 var cnt = Math.min(this.rowsBufferOutSize,this.rowsBuffer[0].length)
 
 for(var i=0;i<cnt;i++){
 var rowNode = this.rowsBuffer[1][0]
 var rId = rowNode.getAttribute("id")
 var cellsCol = rowNode.childNodes;
 var strAr = new Array(0);
 for(var j=0;j<cellsCol.length;j++){
 if(cellsCol.item(j).nodeName=='cell')
 strAr[strAr.length] = cellsCol.item(j).text||cellsCol.item(j).textContent
}
 var r = this._addRow(rId,strAr)
 
 if(rowNode.getAttribute("selected")==true){
 this.setSelectedRow(rId,false,false,rowNode.getAttribute("call")==true)
}
 
 if(rowNode.getAttribute("expand")=="1"){
 r.expand = "";
}
 this.rowsBuffer[0]._dhx_removeAt(0);
 this.rowsBuffer[1]._dhx_removeAt(0);
}
 return true;
}
 
 
 this.setMultiselect = function(fl){
 this.selMultiRows = fl;
}
 
 this.deleteRow = function(row_id,node){
 this.editStop();
 if(typeof(this.onBeforeRowDeleted)=="function" && this.onBeforeRowDeleted(row_id)==false)
 return false;
 if(!node)node = this.getRowById(row_id)

 if(node!=null){
 if(this.cellType._dhx_find("tree")!=-1)this._removeTrGrRow(node);
 node.parentNode.removeChild(node);
 this.rowsCol._dhx_removeAt(this.rowsCol._dhx_find(node))
 node = null;
}
 this.rowsAr[row_id] = null;
 this.setSizes();
}
 
 this.deleteSelectedItem = function(){
 var num = this.selectedRows.length 
 if(num==0)
 return;
 var tmpAr = this.selectedRows;
 this.selectedRows = new Array(0)
 for(var i=num-1;i>=0;i--){
 var node = tmpAr[i]
 
 if(!this.deleteRow(node.idd,node)){
 this.selectedRows[this.selectedRows.length] = node;
}else{
 if(node==this.row){
 var ind = i;
}
}
 
}
 if(ind){
 try{
 if(ind+1>this.rowsCol.length)
 ind--;
 this.selectCell(ind,0,true)
}catch(er){
 this.row = null
 this.cell = null
}
}
}
 
 
 this.getSelectedId = function(){
 var selAr = new Array(0);
 for(var i=0;i<this.selectedRows.length;i++){
 selAr[selAr.length]=this.selectedRows[i].idd
}
 
 
 if(selAr.length==0)
 return null;
 else
 return selAr.join(this.delim);
}
 
 this.getSelectedCellIndex = function(){
 if(this.cell!=null)
 return this.cell._cellIndex;
 else
 return -1;
}
 
 this.getColWidth = function(ind){
 return parseInt(this.cellWidthPX[ind])+((_isFF)?2:0);
}
 
 this.getRowById = function(id){
 var row = this.rowsAr[id]
 if(row)
 return row;
 else
 return null;
}
 
 this.setRowId = function(ind,row_id){
 var r = this.rowsCol[ind]
 this.changeRowId(r.idd,row_id)
}
 
 this.changeRowId = function(oldRowId,newRowId){
 var row = this.rowsAr[oldRowId]
 row.idd = newRowId;
 if(this.UserData[oldRowId]){
 this.UserData[newRowId] = this.UserData[oldRowId]
 this.UserData[oldRowId] = null;
}
 if(this.loadedKidsHash){
 if(this.loadedKidsHash.get(oldRowId)!=null){
 this.loadedKidsHash.put(newRowId,this.loadedKidsHash.get(oldRowId));
 this.loadedKidsHash.remove(oldRowId);
}
 var parentsHash = this.loadedKidsHash.get("hashOfParents")
 if(parentsHash!=null){
 if(parentsHash.get(oldRowId)!=null){
 parentsHash.put(newRowId,row);
 parentsHash.remove(oldRowId);
 this.loadedKidsHash.put("hashOfParents",parentsHash)
}
}
}

 this.rowsAr[oldRowId] = null;
 this.rowsAr[newRowId] = row;
}
 
 
 this.getRowIndex = function(row_id){
 var ind = this.rowsCol._dhx_find(this.getRowById(row_id));
 if(ind!=-1)
 return ind;
 else{
 ind = this.rowsBuffer[0]._dhx_find(row_id)
 if(ind!=-1)
 return ind+this.rowsCol.length;
 return -1;
}
}
 
 this.getRowId = function(ind){
 try{
 return this.rowsCol[parseInt(ind)].idd;
}catch(er){
 return this.rowsBuffer[0][ind-this.rowsCol.length-1]
 
}
}

 
 this.setColumnIds = function(ids){
 if(ids)
 this.columnIds = ids.split(",")
 if(this.hdr.rows[0].cells.length>=this.columnIds.length){
 for(var i=0;i<this.columnIds.length;i++){
 this.hdr.rows[0].cells[i].column_id = this.columnIds[i];
}
}
}
 
 this.getColIndexById = function(id){
 for(var i=0;i<this.hdr.rows[0].cells.length;i++){
 if(this.hdr.rows[0].cells[i].column_id==id)
 return i;
}
}
 
 this.getColumnId = function(cin){
 return this.hdr.rows[0].cells[cin].column_id
}
 
 
 this.getHeaderCol = function(cin){
 return this.hdr.rows[0].cells[Number(cin)].innerHTML;
}
 
 
 this.setRowTextBold = function(row_id){
 this.getRowById(row_id).style.fontWeight = "bold";
}
 
 this.setRowTextStyle = function(row_id,styleString){
 var r = this.getRowById(row_id)

 for(var i=0;i<r.childNodes.length;i++){
 if(_isIE)
 r.childNodes[i].style.cssText = "width:"+r.childNodes[i].style.width+";"+styleString;
 else
 r.childNodes[i].style.cssText = "width:"+r.childNodes[i].style.width+";"+styleString;
}

}
 
 this.setRowTextNormal = function(row_id){
 this.getRowById(row_id).style.fontWeight = "normal";
}
 
 this.isItemExists = function(row_id){
 if(this.getRowById(row_id)!=null)
 return true
 else
 return false
}
 
 this.getAllItemIds = function(separator){
 var ar = new Array(0)
 for(i=0;i<this.rowsCol.length;i++){
 ar[ar.length]=this.rowsCol[i].idd
}
 for(i=0;i<this.rowsBuffer[0].length;i++){
 ar[ar.length]=this.rowsBuffer[0][i]
}
 return ar.join(separator||",")
}
 
 this.getRowsNum = function(){
 return this.rowsCol.length+this.rowsBuffer[0].length;
}
 
 this.getColumnCount = function(){
 return this.hdr.rows[0].cells.length;
}
 
 this.moveRowUp = function(row_id){
 var r = this.getRowById(row_id)
 var rInd = this.rowsCol._dhx_find(r)
 this.rowsCol._dhx_swapItems(rInd,rInd-1)
 try{
 this.obj.firstChild.insertBefore(r,r.previousSibling)
}catch(er){alert('Caught error in MoveRowUp method: '+(er.description||er))}
}
 
 this.moveRowDown = function(row_id){
 var r = this.getRowById(row_id)
 var rInd = this.rowsCol._dhx_find(r)
 this.rowsCol._dhx_swapItems(rInd,rInd+1)
 try{
 this.obj.firstChild.insertBefore(r,r.nextSibling.nextSibling)
}catch(er){alert('Caught error in MoveRowDown method: '+er.description)}
}
 
 this.cells = function(row_id,col){
 if(arguments.length==0){
 var c = this.cell;
 return eval("new eXcell_"+this.cellType[this.cell._cellIndex]+"(c)");
}else{
 var c = this.getRowById(row_id);
 if(!c)return null;
 return eval("new eXcell_"+this.cellType[col]+"(c.childNodes[col])");
}
}
 
 this.cells2 = function(row_index,col){
 var c = this.rowsCol[parseInt(row_index)].cells[parseInt(col)];
 return eval("new eXcell_"+this.cellType[c._cellIndex]+"(c)");
}
 
 this.getCombo = function(col_ind){
 if(this.cellType[col_ind].indexOf('co')==0){
 if(!this.combos[col_ind]){
 this.combos[col_ind] = new dhtmlXGridComboObject();
}
 return this.combos[col_ind];
}else{
 return null;
}
}
 
 this.setUserData = function(row_id,name,value){
 try{
 if(row_id=="")
 row_id = "gridglobaluserdata";
 if(!this.UserData[row_id])
 this.UserData[row_id] = new Hashtable()
 this.UserData[row_id].put(name,value)
}catch(er){
 alert("UserData Error:"+er.description)
}
}
 
 this.getUserData = function(row_id,name){
 try{
 if(row_id=="")
 row_id = "gridglobaluserdata";
 return this.UserData[row_id].get(name)
}catch(er){}
}
 
 
 this.setEditable = function(fl){
 if(fl!='true' && fl!=1 && fl!=true)
 ifl = true;
 else
 ifl = false;
 for(var j=0;j<this.cellType.length;j++){
 if(this.cellType[j].indexOf('ra')==0 || this.cellType[j]=='ch'){
 for(var i=0;i<this.rowsCol.length;i++){
 var z=this.rowsCol[i].cells[j];
 if((z.childNodes.length>0)&&(z.firstChild.nodeType==1)){
 this.rowsCol[i].cells[j].firstChild.disabled = ifl;
}
}
}
}
 this.isEditable = !ifl;
}
 
 this.setSelectedRow = function(row_id,multiFL,show,call){
 if(!call)
 call = false;
 this.selectCell(this.getRowById(row_id),0,call,multiFL);
 if(arguments.length>2 && show==true){
 this.moveToVisible(this.getRowById(row_id).cells[0])
}
}
 
 this.clearSelection = function(){
 this.editStop()
 for(var i=0;i<this.selectedRows.length;i++){
 this.selectedRows[i].className=this.selectedRows[i].className.replace(/rowselected/g,"");
}
 
 
 this.selectedRows = new Array(0)
 this.row = null;
 if(this.cell!=null){
 this.cell.className = "";
 this.cell = null;
}
}
 
 this.copyRowContent = function(from_row_id,to_row_id){
 var frRow = this.getRowById(from_row_id)
 for(i=0;i<frRow.cells.length;i++){
 this.cells(to_row_id,i).setValue(this.cells(from_row_id,i).getValue())
}
 
 if(!isIE())
 this.getRowById(from_row_id).cells[0].height = frRow.cells[0].offsetHeight
}
 
 this.setHeaderCol = function(col,label){
 this.hdr.rows[0].cells[Number(col)].innerHTML = label;
}
 
 this.clearAll = function(){
 this.editStop();
 
 var len = this.rowsCol.length;
 for(var i=len-1;i>=0;i--){
 this.obj.firstChild.removeChild(this.rowsCol[i])
 this.rowsCol._dhx_removeAt(i);
}
 
 if(this.loadedKidsHash!=null){
 this.loadedKidsHash.clear();
 this.loadedKidsHash.put("hashOfParents",new Hashtable());
}
 
 len = this.obj.rows.length
 for(var i=len-1;i>=0;i--){
 this.obj.firstChild.removeChild(this.obj.rows[i])
}
 
 this.row = null;
 this.cell = null;
 this.rowsAr = new Array(0)
 this.rowsCol = new Array(0)
 this.rowsAr = new Array(0);
 this.rowsBuffer = new Array(new Array(0),new Array(0));
 this.UserData = new Array(0)
 
}
 this._sortField = function(ev){
 var el = this.getFirstParentOfType(ev.target||ev.srcElement,"TD");
 this.sortField(el._cellIndex)
}
 
 this.sortField = function(ind,repeatFl){
 if(this.getRowsNum()==0)
 return false;
 var el = this.hdr.rows[0].cells[ind];
 if(el.tagName == "TD" &&(this.fldSort.length-1)>=el._cellIndex && this.fldSort[el._cellIndex]!='na'){
 if(((this.sortImg.src.indexOf("_desc.gif")==-1 && !repeatFl)||(this.sortImg.style.filter!="" && repeatFl))&& this.fldSorted==el){
 var sortType = "desc";
 this.sortImg.src = this.imgURL+"sort_desc.gif";
}else{
 var sortType = "asc";
 this.sortImg.src = this.imgURL+"sort_asc.gif";
}
 this.sortRows(el._cellIndex,this.fldSort[el._cellIndex],sortType)
 this.fldSorted = el;
 this.setSortImgPos();
}
}
 
 
 this.setHeader = function(hdrStr){
 var arLab = hdrStr.split(this.delim);
 var arWdth = new Array(0);
 var arTyp = new Array(0);
 var arAlg = new Array(0);
 var arSrt = new Array(0);
 for(var i=0;i<arLab.length;i++){
 arWdth[arWdth.length] = Math.round(100/arLab.length);
 arTyp[arTyp.length] = "ed";
 arAlg[arAlg.length] = "left";
 arSrt[arSrt.length] = "na";
}
 this.hdrLabels = arLab;
 this.cellWidth = arWdth;
 this.cellType = arTyp;
 this.cellAlign = arAlg;
 this.fldSort = arSrt;
}
 
 this.setColTypes = function(typeStr){
 this.cellType = typeStr.split(this.delim)
}
 
 this.setColSorting = function(sortStr){
 this.fldSort = sortStr.split(this.delim)
}
 
 this.setColAlign = function(alStr){
 this.cellAlign = alStr.split(this.delim)
}
 
 
 this.setMultiLine = function(fl){
 if(fl==true)
 this.multiLine = -1;
}
 
 this.setNoHeader = function(fl){
 if(fl==true)
 this.noHeader = -1;
}
 
 this.showRow = function(rowID){
 this.moveToVisible(this.getRowById(rowID).cells[0])
}
 
 
 this.setStyle = function(ss_header,ss_grid,ss_selCell,ss_selRow){
 this.ssModifier = new Array(4)
 this.ssModifier[0] = ss_header;
 this.ssModifier[1] = ss_grid;
 this.ssModifier[2] = ss_selCell;
 this.ssModifier[3] = ss_selRow;
 this.styleSheet[0].addRule("#"+this.entBox.id+" table.hdr td",this.ssHeader+""+this.ssModifier[0]);
 this.styleSheet[0].addRule("#"+this.entBox.id+" table.obj td",this.ssGridCell+""+this.ssModifier[1]);
 this.styleSheet[0].addRule("#"+this.entBox.id+" table.obj tr.rowselected td.cellselected",this.ssSelectedCell+""+this.ssModifier[2]);
 this.styleSheet[0].addRule("#"+this.entBox.id+" table.obj td.cellselected",this.ssSelectedCell+""+this.ssModifier[2])
 this.styleSheet[0].addRule("#"+this.entBox.id+" table.obj tr.rowselected td",this.ssSelectedRow+""+this.ssModifier[3]);
}
 
 this.setColumnColor = function(clr){
 this.columnColor = clr.split(this.delim)
}
 
 
 this.doDynScroll = function(fl){
 if(!this.dynScroll || this.dynScroll=='false')
 return false;
 this.objBox.style.overflowY = "hidden";
 this.setDynScrollPageSize();
 
 var tmpAr = new Array(0)
 if(fl && fl=='up'){
 this.dynScrollPos = Math.max(this.dynScrollPos-this.dynScrollPageSize,0);
}else if(fl && fl=='dn' && this.dynScrollPos+this.dynScrollPageSize<this.rowsCol.length){
 if(this.dynScrollPos+this.dynScrollPageSize+this.rowsBufferOutSize>this.rowsCol.length){
 this.addRowsFromBuffer()
}
 this.dynScrollPos+=this.dynScrollPageSize
}
 var start = Math.max(this.dynScrollPos-this.dynScrollPageSize,0);
 for(var i = start;i<this.rowsCol.length;i++){
 if(i>=this.dynScrollPos && i<this.dynScrollPos+this.dynScrollPageSize){
 tmpAr[tmpAr.length] = this.rowsCol[i];
}
 this.rowsCol[i].removeNode(true);
}
 for(var i=0;i<tmpAr.length;i++){
 this.obj.childNodes[0].appendChild(tmpAr[i]);
 if(this.obj.offsetHeight>this.objBox.offsetHeight)
 this.dynScrollPos-=(this.dynScrollPageSize-i)
}
 this.setSizes()
}
 
 this.setDynScrollPageSize = function(){
 if(this.dynScroll && this.dynScroll!='false'){
 var rowsH = 0;
 try{
 var rowH = this.obj.rows[0].scrollHeight;
}catch(er){
 var rowH = 20
}
 for(var i=0;i<1000;i++){
 rowsH = i*rowH;
 if(this.objBox.offsetHeight<rowsH)
 break
}
 this.dynScrollPageSize = i+2;
 this.rowsBufferOutSize = this.dynScrollPageSize*4
}
}


 
 
 
 this.setOnRowSelectHandler = function(func){
 if(!func)
 this.onRowSelect = "void";
 else
 if(typeof(func)=="function")
 this.onRowSelect=func;
 else 
 this.onRowSelect=eval(func);
}
 
 
 this.setOnEditCellHandler = function(func){
 if(!func)
 this.onEditCell = "void";
 else
 if(typeof(func)=="function")
 this.onEditCell=func;
 else 
 this.onEditCell=eval(func);
}
 
 this.setOnCheckHandler = function(func){
 if(!func)
 this.onCheckbox = null;
 else
 if(typeof(func)=="function")
 this.onCheckbox=func;
 else 
 this.onCheckbox=eval(func);
}
 
 
 this.setOnEnterPressedHandler = function(func){
 if(!func)
 this.onEnter = "void";
 else
 if(typeof(func)=="function")
 this.onEnter=func;
 else 
 this.onEnter=eval(func);
}
 
 
 this.setOnBeforeRowDeletedHandler = function(func){
 if(!func)
 this.onBeforeRowDeleted = "void";
 else
 if(typeof(func)=="function")
 this.onBeforeRowDeleted=func;
 else 
 this.onBeforeRowDeleted=eval(func);
}
 
 this.setOnRowAddedHandler = function(func){
 if(!func)
 this.onRowAdded = "void";
 else
 if(typeof(func)=="function")
 this.onRowAdded=func;
 else 
 this.onRowAdded=eval(func);
}
 
 
 
 
 this.getPosition = function(oNode,pNode){
 if(!pNode)
 var pNode = document.body
 var oCurrentNode=oNode;
 var iLeft=0;
 var iTop=0;
 while((oCurrentNode)&&(oCurrentNode!=pNode)){
 iLeft+=oCurrentNode.offsetLeft;
 iTop+=oCurrentNode.offsetTop;
 oCurrentNode=oCurrentNode.offsetParent;
}
 if(((_isKHTML)||(_isOpera))&&(pNode == document.body)){
 iLeft+=document.body.offsetLeft;
 iTop+=document.body.offsetTop;
}

 return new Array(iLeft,iTop);
}
 
 this.getFirstParentOfType = function(obj,tag){
 while(obj.tagName!=tag && obj.tagName!="BODY"){
 obj = obj.parentNode;
}
 return obj;
}

 
 
 this.setColumnCount = function(cnt){alert('setColumnCount method deprecated')}
 
 this.showContent = function(){alert('showContent method deprecated')}
 
 
 this.objBox.onscroll = new Function("","this.grid.doOnScroll()")
 if(!_isOpera)
{
 this.hdr.onmousemove = new Function("e","this.grid.changeCursorState(e||window.event)");
 this.hdr.onmousedown = new Function("e","this.grid.startColResize(e||window.event)");
}
 this.obj.onmousemove = new Function("e","try{if(!this.grid.editor){var c = this.grid.getFirstParentOfType(e?e.target:event.srcElement,'TD');var r = c.parentNode;var ced = this.grid.cells(r.idd,c._cellIndex);if(!ced)return false;(e?e.target:event.srcElement).title = ced.getTitle?ced.getTitle():ced.getValue()}}catch(er){}");
 this.entBox.onclick = new Function("e","this.grid._doClick(e||window.event)");
 this.obj.ondblclick = new Function("e","this.grid.editCell(e||window.event)");
 this.hdr.onclick = new Function("e","if(this.grid.resized==null)this.grid._sortField(e||window.event);");
 
 document.onkeydown = new Function("e","if(globalActiveDHTMLGridObject)return globalActiveDHTMLGridObject.doKey(e||window.event);return true;");
 
 
 this.entBox.onbeforeactivate = new Function("","this.grid.setActive()");
 this.entBox.onbeforedeactivate = new Function("","this.grid.isActive=-1");
 
 this.doOnRowAdded = function(row){};

}



 dhtmlXGridObject.prototype.isTreeGrid= function(){
 return(this.cellType._dhx_find("tree")!=-1);
}

 
 dhtmlXGridObject.prototype.addRow = function(new_id,text,ind){
 var r = this._addRow(new_id,text,ind);
 if(typeof(this.onRowAdded)=='function'){
 this.onRowAdded(new_id);
}
 this.setSizes();
 return r;
}
 
 dhtmlXGridObject.prototype._addRow = function(new_id,text,ind){
 if(ind<0)ind=this.obj.rows.length;
 
 this.math_off=true;
 this.math_req=false;

 if((arguments.length<3)||(ind===window.undefined))
 ind = this.rowsCol.length 
 else{
 if(ind>this.rowsCol.length)
 ind = this.rowsCol.length;
}
 if(typeof(text)!='object')
 text = text.split(this.delim)



 if((!this.dynScroll || this.dynScroll=='false' || ind<this.obj.rows.length)&&((ind)||(ind==0)))
{
 if((_isKHTML)&&(ind==this.obj.rows.length)){
 var r=document.createElement("TR");
 this.obj.appendChild(r);
}
 else
 var r=this.obj.insertRow(ind);
}
 else
 var r = this.obj.insertRow(this.obj.rows.length);

 if(this.multiLine != true)
 this.obj.className+=" row20px";


 r.idd = new_id;
 r.grid = this;
 
 for(var i=0;i<this.hdr.rows[0].cells.length;i++){
 var c = r.insertCell(i)
 c._cellIndex = i;
 if(this.dragAndDropOff)this.dragger.addDraggableItem(c,this);
 c.align = this.cellAlign[i]
 
 c.bgColor = this.columnColor[i] || ""
 this.editStop();
 if(i<text.length){
 var val = text[i]
 if((this.defVal[i])&&(val==""))val = this.defVal[i];

 eval("this.editor = new eXcell_"+this.cellType[i]+"(c)");
 this.editor.setValue(val)
 this.editor = this.editor.destructor();
}else{
 var val = "&nbsp;";
 c.innerHTML = val;
}

}

 this.rowsAr[new_id] = r;
 this.rowsCol._dhx_insertAt(ind,r);

 

 this.chngCellWidth(ind)
 this.doOnRowAdded(r);
 

 this.math_off=false;
 if((this.math_req)&&(!this._parsing_)){
 for(var i=0;i<this.hdr.rows[0].cells.length;i++)
 this._checkSCL(r.childNodes[i]);
}
 return r;

}


dhtmlXGridObject.prototype.destructor=function(){
 var a;
 this.xmlLoader=this.xmlLoader.destructor();
 for(var i=0;i<this.rowsCol.length;i++)
 this.rowsCol[i].grid=null;
 for(var i=0;i<this.rowsAr.length;i++)
 if(this.rowsAr[i])this.rowsAr[i].grid=null;

 this.rowsCol=new Array();
 this.rowsAr=new Array();
 this.entBox.innerHTML="";
 this.entBox.onclick = function(){};
 this.entBox.onmousedown = function(){};
 this.entBox.onbeforeactivate = function(){};
 this.entBox.onbeforedeactivate = function(){};
 this.entBox.onbeforedeactivate = function(){};

 for(a in this)
 this[a]=null;


 if(this==globalActiveDHTMLGridObject)
 globalActiveDHTMLGridObject=null;
 return null;
}

