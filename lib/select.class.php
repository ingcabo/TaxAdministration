<? 
class select {
	var $form;
	var $select_parent;
	var $select_child;
	var $select_child2;
	var $emptymessage='Selecione';
	
	function select($form) {
		$this->form=$form;
	}
	function create_parent($id,$name) {
		$p=new select_list;
		$p->id=$id;
		$p->name=$name;
		$this->select_parent=$p;
	}
	function create_child($id,$name) {
		$p=new select_list;
		$p->id=$id;
		$p->name=$name;
		$this->select_child=$p;
	}
	function create_child2($id,$name) {
		$p=new select_list;
		$p->id=$id;
		$p->name=$name;
		$this->select_child2=$p;
	}

	function add_option_parent($id,$text){
		$this->select_parent->add_option($text,$id);	
	}

	function add_option_child($value, $texto, $id_option_parent){
		$this->select_child->add_option($texto,$value,$id_option_parent);
		$this->select_parent->add_option($text,$value);
	}
	
	function add_option_child2($value, $texto, $id_option_parent){
		$this->select_child2->add_option2($texto,$value,$id_option_parent);
	}

	function print_script() {
		echo '<script>'."\n" ;
		echo "function loadchild(){\n" ;

		foreach($this->select_parent->option as $poptions) {
			$art= "    var art_".$poptions->value."=new Array('',";
			$art_name="    var art_name_".$poptions->value."=new Array('".$this->emptymessage."',";
			$arr_child=$this->select_child->get_array_of_parent($poptions->value);
			foreach($arr_child as $childopt){
				$art.='"'.$childopt->value.'",';
				$art_name.='"'.$childopt->text.'",';
			}
				$art=substr($art,0,strlen($art)-1). ")\n" ; //quitar la última , y cerrar el )
				$art_name=substr($art_name,0,strlen($art_name)-1). ")\n" ; //quitar la última , y cerrar
			echo $art;
			echo $art_name;
		}
		echo "    c = document.". $this->form .".".$this->select_parent->name."[document.". $this->form .".".$this->select_parent->name.".selectedIndex].value;\n" ;
		echo '    seleccion=eval("art_"+c);//formo por ej art_1 para los art de la cat 1'."\n" ; 
		echo '    seleccionNames=eval("art_name_"+c);//igual pero con el nombre'. "\n" ; 
		echo '    cuantos_add=seleccion.length;//obtengo el numero de elementos del array'."\n" ;
		echo '    document.'.$this->form.'.'.$this->select_child->name.'.length=cuantos_add;//establesco el nuevo numero de elementos del combo '."\n" ;
		echo '    for(i=0;i<cuantos_add;i++){ '."\n" ;
		echo  '        document.'.$this->form.'.'.$this->select_child->name.'.options[i].value=seleccion[i];'."\n" ;
		echo  '        document.'.$this->form.'.'.$this->select_child->name.'.options[i].text=seleccionNames[i];'."\n";
		echo '    }'."\n" ;
		echo "}\n";
		echo "</script>";
	}
	function print_script2() {
		echo '<script>'."\n" ;
		echo "function loadchild2(){\n" ;

		foreach($this->select_child->option as $poptions) {
			$art= "    var art_".$poptions->value."=new Array('',";
			$art_name="    var art_name_".$poptions->value."=new Array('".$this->emptymessage."',";
			$arr_child=$this->select_child2->get_array_of_parent2($poptions->value);
			foreach($arr_child as $childopt){
				$art.='"'.$childopt->value.'",';
				$art_name.='"'.$childopt->text.'",';
			}
				$art=substr($art,0,strlen($art)-1). ")\n" ; //quitar la última , y cerrar el )
				$art_name=substr($art_name,0,strlen($art_name)-1). ")\n" ; //quitar la última , y cerrar
			echo $art;
			echo $art_name;
		}
		echo "    c = document.". $this->form .".".$this->select_child->name."[document.". $this->form .".".$this->select_child->name.".selectedIndex].value;\n" ;
		echo '    seleccion=eval("art_"+c);//formo por ej art_1 para los art de la cat 1'."\n" ; 
		echo '    seleccionNames=eval("art_name_"+c);//igual pero con el nombre'. "\n" ; 
		echo '    cuantos_add=seleccion.length;//obtengo el numero de elementos del array'."\n" ;
		echo '    document.'.$this->form.'.'.$this->select_child2->name.'.length=cuantos_add;//establesco el nuevo numero de elementos del combo '."\n" ;
		echo '    for(i=0;i<cuantos_add;i++){ '."\n" ;
		echo  '        document.'.$this->form.'.'.$this->select_child2->name.'.options[i].value=seleccion[i];'."\n" ;
		echo  '        document.'.$this->form.'.'.$this->select_child2->name.'.options[i].text=seleccionNames[i];'."\n";
		echo '    }'."\n" ;
		echo "}\n";
		echo "</script>";
	}
	function call_function() {
		echo "onchange=loadchild()";
	}
	function call_function2() {
		echo "onchange=loadchild2()";
	}
}
class select_list{
	var $id;
	var $name;
	var $option=array();
	var $option2=array();
	
	function add_option($text,$value,$optparent="") {
		$opt=new option_list;
		$opt->text=$text;
		$opt->value=$value;
		if ($optparent!=0) {
			$opt->optparent=$optparent;
		}
		$this->option[]=$opt;
	}
	
	function add_option2($text,$value,$optparent="") {
		$opt=new option_list2;
		$opt->text=$text;
		$opt->value=$value;
		if ($optparent!=0) {
			$opt->optparent=$optparent;
		}
		$this->option2[]=$opt;
	}

	function get_array_of_parent($idparent) {
		$arrp=array();
		foreach($this->option as $option) {
			if ($option->optparent==$idparent){
				$arrp[]=$option;
			}
		}
		return $arrp;
	}
	function get_array_of_parent2($idparent) {
		$arrp=array();
		foreach($this->option2 as $option) {
			if ($option->optparent==$idparent){
				$arrp[]=$option;
			}
		}
		return $arrp;
	}
}

class option_list {
	var $text;
	var $value;
	var $optparent=""; //indica cual es el option relacionado en caso de ser un hijo
}

class option_list2 {
	var $text;
	var $value;
	var $optparent=""; //indica cual es el option relacionado en caso de ser un hijo
}
?>
