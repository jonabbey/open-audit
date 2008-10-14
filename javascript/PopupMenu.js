//***********************************************
//Pop-it menu- © Dynamic Drive (www.dynamicdrive.com)
//This notice MUST stay intact for legal use
//Visit http://www.dynamicdrive.com/ for full source code
//***********************************************
var oMenu;
var bIe = document.all && !window.opera

document.onclick = HideMenu;

//***********************************************
// get and identify the source of the event object
//***********************************************
function getEventTarget(x)
{ 
	x = x || window.event;
	return x.target || x.srcElement;
} 

function ShowMenu(e, MenuHTML)
{
	if (!document.all && !document.getElementById) return;
	ClearHideMenu(e);
	oMenu = document.getElementById("npb_popupmenu_div");
	oMenu.innerHTML = MenuHTML;
	oMenu.style.width = "120px";
	oMenu.contentwidth = oMenu.offsetWidth;
	oMenu.contentheigh = oMenu.offsetHeight;
	eventX = e.clientX;
	eventY = e.clientY;
	
	//Find out how close the mouse is to the corner of the window
	var RightEdge = bIe ? GetIEDocBody().clientWidth - eventX : window.innerWidth-eventX
	var BottomEdge = bIe ? GetIEDocBody().clientHeight - eventY : window.innerHeight-eventY
	
	//if the horizontal distance isn't enough to accomodate the width of the context menu
	if (RightEdge < oMenu.contentwidth) 
	{oMenu.style.left = bIe ? GetIEDocBody().scrollLeft + eventX - oMenu.contentwidth + "px" : window.pageXOffset + eventX - oMenu.contentwidth + "px";}
	//move the horizontal position of the menu to the left by it's width
	else
	//position the horizontal position of the menu where the mouse was clicked
	{oMenu.style.left = bIe ? GetIEDocBody().scrollLeft+eventX + "px" : window.pageXOffset + eventX + "px"}
	
	//same concept with the vertical position
	if (BottomEdge < oMenu.contentheight)
	{oMenu.style.top = bIe ? GetIEDocBody().scrollTop + eventY - oMenu.contentheight + "px" : window.pageYOffset + eventY - oMenu.contentheight + "px";}
	else {oMenu.style.top = bIe ? GetIEDocBody().scrollTop + event.clientY + "px" : window.pageYOffset + eventY + "px";}

	// Added by Nick Brown - kludge to pass the id of the link to the npb_popupmenu_div via a hidden DIV on the menu
	document.getElementById("uid").innerHTML = getEventTarget(e).id;
	
	oMenu.style.visibility = "visible";
	return false;
}

function HideMenu()
{if (window.oMenu) oMenu.style.visibility = "hidden";}

function DelayHideMenu()
{DelayHide = setTimeout("HideMenu()",300);}

function ClearHideMenu(e)
{if (window.DelayHide) clearTimeout(DelayHide);}

function DynamicHide(e)
{if (e.currentTarget != e.relatedTarget && !Contains(e.currentTarget, e.relatedTarget)) HideMenu();}

function Contains(a, b)
{
	//Determines if 1 element in contained in another- by Brainjar.com
	while (b.parentNode)
	{if ((b = b.parentNode) == a) return true;}
	return false;
}

function GetIEDocBody()
{
	return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}



/*
//***********************************************
//Pop-it menu- © Dynamic Drive (www.dynamicdrive.com)
//This notice MUST stay intact for legal use
//Visit http://www.dynamicdrive.com/ for full source code
//***********************************************
var defaultMenuWidth="150px" //set default menu width.

var linkset=new Array()
//SPECIFY MENU SETS AND THEIR LINKS. FOLLOW SYNTAX LAID OUT

linkset[0]='<a href="http://dynamicdrive.com">Dynamic Drive</a>'
linkset[0]+='<hr>' //Optional Separator
linkset[0]+='<a href="http://www.javascriptkit.com">JavaScript Kit</a>'
linkset[0]+='<a href="http://www.codingforums.com">Coding Forums</a>'
linkset[0]+='<a href="http://www.cssdrive.com">CSS Drive</a>'
linkset[0]+='<a href="http://freewarejava.com">Freewarejava</a>'

linkset[1]='<a href="http://msnbc.com">MSNBC</a>'
linkset[1]+='<a href="http://cnn.com">CNN</a>'
linkset[1]+='<a href="http://news.bbc.co.uk">BBC News</a>'
linkset[1]+='<a href="http://www.washingtonpost.com">Washington Post</a>'

////No need to edit beyond here

var ie5=document.all && !window.opera
var ns6=document.getElementById

if (ie5||ns6)
document.write('<div id="popitmenu" onMouseover="clearhidemenu();" onMouseout="dynamichide(event)"></div>')

function iecompattest(){
return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}

function showmenu(e, which, optWidth){
if (!document.all&&!document.getElementById)
return
clearhidemenu()
menuobj=ie5? document.all.popitmenu : document.getElementById("popitmenu")
menuobj.innerHTML=which
menuobj.style.width=(typeof optWidth!="undefined")? optWidth : defaultMenuWidth
menuobj.contentwidth=menuobj.offsetWidth
menuobj.contentheight=menuobj.offsetHeight
eventX=ie5? event.clientX : e.clientX
eventY=ie5? event.clientY : e.clientY
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? iecompattest().clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie5? iecompattest().clientHeight-eventY : window.innerHeight-eventY
//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by it's width
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX-menuobj.contentwidth+"px" : window.pageXOffset+eventX-menuobj.contentwidth+"px"
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX+"px" : window.pageXOffset+eventX+"px"
//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.style.top=ie5? iecompattest().scrollTop+eventY-menuobj.contentheight+"px" : window.pageYOffset+eventY-menuobj.contentheight+"px"
else
menuobj.style.top=ie5? iecompattest().scrollTop+event.clientY+"px" : window.pageYOffset+eventY+"px"
menuobj.style.visibility="visible"
return false
}

function contains_ns6(a, b) {
//Determines if 1 element in contained in another- by Brainjar.com
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function hidemenu(){
if (window.menuobj)
menuobj.style.visibility="hidden"
}

function dynamichide(e){
if (ie5&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}

if (ie5||ns6)
document.onclick=hidemenu

</script>
*/