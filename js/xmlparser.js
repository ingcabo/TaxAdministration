XMLDoc = Class.create();
Object.extend(XMLDoc.prototype, {
	initialize: function (xmlDoc) {
		this.element = xmlDoc;
	},
	asHash: function () {
		if (! this._xmlHash) {
			this._xmlHash = this._nodeToHash(this.element);
		}
		return this._xmlHash;
	},
	_nodeToHash: function (node) {
		Element.cleanWhitespace(node);
		if ((node.attributes && node.attributes.length > 0) || (node.hasChildNodes() && node.childNodes[0].nodeType == 1)) {
			var localHash = {};
			if (node.attributes && node.attributes.length >= 1) {
				$A(node.attributes).each(function (attr) { localHash[attr.nodeName] = [attr.nodeValue]; });
			}
			if (node.hasChildNodes() && node.childNodes[0].nodeType == 1) {$A(node.childNodes).each(function (node) {
				this._subNodeToHash(localHash, node); }.bindAsEventListener(this));
			}
			else if (node.hasChildNodes()) {
				localHash['text'] = [this._nodeAsText(node)];
			}
			$H(localHash).each( function (pair) { if (pair[1].length == 1 && typeof pair[1][0] == 'string') { localHash[pair[0]] = pair[1][0]; } });
			return localHash;
		}
		else {
			return this._nodeAsText(node);
		}
	},
	 _subNodeToHash: function (hash, node) {
			var key = node.tagName;
			if (hash[key]) {
				hash[key].push(this._nodeToHash(node));
			}
			else {
				hash[key] = [ this._nodeToHash(node) ];
			}
	},
	_nodeAsText: function (node) {
		return node.textContent || node.innerText || node.text || '';
	}
} );