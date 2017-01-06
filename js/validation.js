/*
 * Really easy field validation with Prototype
 * http://tetlaw.id.au/view/blog/really-easy-field-validation-with-prototype
 * Andrew Tetlaw 2006-04-27
 * 
 * http://creativecommons.org/licenses/by-sa/2.5/
 */
Validator = Class.create();

Validator.prototype = {
	initialize : function(className, error, test) {
		this.test = test ? test : function(){ return true };
		this.error = error ? error : 'Validation failed.';
		this.className = className;
	}
}

var Validation = Class.create();

Validation.prototype = {
	initialize : function(form, options){
		this.options = Object.extend({stopOnFirst : false}, options || {});
		this.form = $(form);
		Event.observe(this.form,'submit',this.onSubmit.bind(this),false);
	},
	onSubmit :  function(ev){
		if(!this.validate()) Event.stop(ev);
	},
	validate : function() {
		if(this.options.stopOnFirst) {
			return Form.getElements(this.form).all($V);
		} else {
			var test = Form.getElements(this.form).collect($V);
			return test.all();
		}
	}
}

Object.extend(Validation, {
	validate : function(elm, index, options){ // index is here only because we use this function in Enumerations
		var options = Object.extend({}, options || {}); // options still under development and here as a placeholder only
		elm = $(elm);
		var cn = elm.classNames();
		return result = cn.all(Validation.test.bind(elm));
	},
	test : function(name) {
		var v = $VG(name);
		var id = 'advice-' + name + '-' + this.id;
		var prop = '__advice'+name;
		if(!v.test($F(this))) {
			if(!this[prop]) {
				var advice = document.createElement('div');
				advice.appendChild(document.createTextNode(v.error));
				advice.className = 'validation-advice';
				advice.id = id;
				advice.style.display = 'none';
				this.parentNode.insertBefore(advice, this.nextSibling);
				if(typeof Effect == 'undefined') {
					advice.style.display = 'block';
				} else {
					new Effect.Appear(advice.id, {duration : 1 });
				}
			}
			this[prop] = true;
			this.removeClassName('validation-passed');
			this.addClassName('validation-failed');
			return false;
		} else {
			try {
				$(id).remove();
			} catch(e) {}
			this[prop] = '';
			this.removeClassName('validation-failed');
			this.addClassName('validation-passed');
			return true;
		}
	},
	add : function(className, error, test, options) {
		var nv = {}
		nv[className] = new Validator(className, error, test, options);
		Object.extend(Validation.methods, nv);
	},
	get : function(name) {
		return  Validation.methods[name] ? Validation.methods[name] : new Validator();
	},
	methods : {}
});

var $V = Validation.validate;
var $VG = Validation.get;
var $VA = Validation.add;

$VA('IsEmpty', '', function(v) {
				return  ((v == null) || (v.length == 0) || /^\s+$/.test(v));
			});
$VA('required', 'Este campo es requerido.', function(v) {
				return !$VG('IsEmpty').test(v);
			});
$VA('validate-number', 'Por favor solo utilice n&uacute;meros en este campo.', function(v) {
				return $VG('IsEmpty').test(v) || !isNaN(v);
			});
$VA('validate-digits', 'Por favor solo utilice n&uacute;meros en este campo.', function(v) {
				return $VG('IsEmpty').test(v) ||  !/[^\d]/.test(v);
			});
$VA('validate-alpha', 'Por favor solo utilice lestras en este campo.', function (v) {
				return $VG('IsEmpty').test(v) ||  /^[a-zA-Z]+$/.test(v)
			});
$VA('validate-alphanum', 'Por favor solo utilice letras y n&uacute;meros en este campo. no son permitidos espacios u otros caracteres.', function(v) {
				return $VG('IsEmpty').test(v) ||  !/\W/.test(v)
			});
$VA('validate-date', 'Por favor utilice una fecha v&aacute;lida, mm/dd/aaaa.', function(v) {
				var test = new Date(v);
				return $VG('IsEmpty').test(v) || !isNaN(test);
			});
$VA('validate-email', 'Por favor introduzca una direcci&oacute;n de correo v&aacute;lida. Por ejemplo pepe@dominio.com .', function (v) {
				return $VG('IsEmpty').test(v) || /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/.test(v)
			});
$VA('validate-date-au', 'Por favor utilice una fecha v&aacute;lida, dd/mm/aaaa.', function(v) {
				if(!$VG('IsEmpty').test(v)) {
					var upper = 31;
					if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(v)) { // dd/mm/yyyy
						if(RegExp.$2 == '02') upper = 29;
						if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return true;
				}
			});
$VA('validate-currency-dollar', 'Please enter a valid $ amount. For example $100.00 .', function(v) {
				// [$]1[##][,###]+[.##]
				// [$]1###+[.##]
				// [$]0.##
				// [$].##
				return $VG('IsEmpty').test(v) ||  /^\$?\-?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}\d*(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/.test(v)
			});

