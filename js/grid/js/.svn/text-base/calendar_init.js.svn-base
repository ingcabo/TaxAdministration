

YAHOO.widget.Calendar.prototype._arender=YAHOO.widget.Calendar.prototype.render;
YAHOO.widget.Calendar.prototype.render=function(x,y,obj,val){
    if (obj)
    {
        this.onSelect=function(){
            if (!obj._skip_detach)
                obj.grid.editStop();//detach(this);
            else obj._skip_detach=false;
        }
    }

    if (val) {
        
		var z=val.split("/");
        this.setYear(z[2]);
        this.setMonth(z[1]-1);
        obj._skip_detach=true;
		this.select((z[0])+"/"+z[1]+"/"+z[2]);
        }
    this._arender();
    if (x){
    this._myCont.style.display="";
    this._myCont.style.position="absolute";
    this._myCont.zIndex="19";
    this._myCont.style.top=y+"px";
    this._myCont.style.left=x+"px";
    }

}
YAHOO.widget.Calendar.prototype.hide=function(){
    this._myCont.style.display="none";
}



function  _grid_calendar_init(){
var z=document.createElement("DIV");
z.style.display="none";
z.id="_cal_"+((new Date()).valueOf());
document.body.appendChild(z);

window._grid_calendar = new YAHOO.widget.Calendar("_grid_calendar", z.id);

window._grid_calendar.customConfig = function(){ 
		this.Config.Locale.MDY_MONTH_POSITION = 2;  //The value used to determine the position of the month in a month/day/year combo (e.g. 12/25/2005) (Default: 1)
		this.Config.Locale.MDY_DAY_POSITION = 1;
		this.Config.Locale.MDY_YEAR_POSITION = 3; //The value used to determine the position of the year in a month/day/year combo (e.g. 12/25/2005) (Default: 3)
		this.Config.Locale.MONTHS_SHORT = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dec"]; 
	    this.Config.Locale.MONTHS_LONG = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]; 
	    this.Config.Locale.WEEKDAYS_SHORT = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"]; 
	}	

window._grid_calendar.setupConfig();
window._grid_calendar._myCont=z;
window._grid_calendar.render();
window._grid_calendar.hide();
}
