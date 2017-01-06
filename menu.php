<? require("comun/ini.php");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
<title>menu</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/tree.css" />
<script type="text/javascript" src="js/yahooLibs/yahoo.js" ></script>
<script type="text/javascript" src="js/yahooLibs/treeview.js" ></script>
</head>
<body onLoad="treeInit()">
<div id="menuDiv"></div>
<script type="text/javascript">
var tree;
<?
$oMenu = new menu;
// obtengo un array con los modulos a los que el usuario tiene permiso
$cModulos = $oMenu->getAllModulosPorUsuario($conn, $usuario->id);
$nivel = 1;
?>
function treeInit() {
	tree = new YAHOO.widget.TreeView("menuDiv");
	var root = tree.getRoot();
	var myobj;
<?
	if(is_array($cModulos)){
		for($i=0; $i<count($cModulos);$i++){
?>
			myobj = { label: "<?=$cModulos[$i]->descripcion?>"};
			var nodoTmp_<?=$cModulos[$i]->id?> = new YAHOO.widget.TextNode(myobj, root, false);
<? 
			$cNivel1 = $oMenu->getAllOpsPorUsuarioModuloNivel($conn, $usuario->id, $cModulos[$i]->id, $nivel);
			if(is_array($cNivel1)){
				for($j=0; $j<count($cNivel1);$j++){
					$nodoTmp = "nodoTmp_".$cModulos[$i]->id."_".$cNivel1[$j]->id;
					if($cNivel1[$j]->tipo == 'V')
						$link = ", href: \"".$cNivel1[$j]->pagina."\", target: \"contenido\"";
					else
						$link="";
?>
					myobj = { label: "<?=$cNivel1[$j]->descripcion?>" <?=$link?>};
					var <?=$nodoTmp?> = new YAHOO.widget.TextNode(myobj, nodoTmp_<?=$cModulos[$i]->id?>, false);
<?
					echo $oMenu->getSubCarpetas($conn, $usuario->id, $cNivel1[$j]->id, $nodoTmp);
				}
			}
		}
	}
?>
	myobj = { label: "Salir", href: "salir.php", target: "contenido"};
	var salir = new YAHOO.widget.TextNode(myobj, root, false);

	tree.draw();
}
</script>
</body>
</html>
