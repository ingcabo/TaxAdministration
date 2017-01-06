var Slideshow = {};
Slideshow.Options = {
  effectPairs: {
    appear: {
      show:   function (element, options) {
        new Effect.Appear(element, options);
      },
      hide:    function (element, options) {
        new Effect.Fade(element, options);
      }
    },
    grow: {
      show:   function (element, options) {
        new Effect.Grow(element, options);
      },
      hide:    function (element, options) {
        new Effect.Shrink(element, options);
      }
    },
    blind: {
      show:   function (element, options) {
        new Effect.BlindDown(element, options);
      },
      hide:    function (element, options) {
        new Effect.BlindUp(element, options);
      }
    },
    slide: {
      show:   function (element, options) {
        new Effect.SlideDown(element, options);
      },
      hide:    function (element, options) {
        new Effect.SlideUp(element, options);
      }
    }
  },
  defaults: {
    minFrames:      2,
    delay:          5,
    randomize:      false,
    repeat:         false,
    effectPair:     'appear',
    showEffect:     null,
    showOptions:    {},
    hideEffect:     null,
    hideOptions:    {},
    beforeChange:   null,
    afterChange:    null,
    startPaused:    false
  }
};

Effect.Slideshow = Class.create();
Object.extend(Object.extend(Effect.Slideshow.prototype, Effect.Base.prototype), {
  initialize: function (element, options) {
    this.element = $(element);
    this._shared_initialize(options);
  },
  _shared_initialize: function (options) {
    Element.cleanWhitespace(this.element);

    this.options = Slideshow.Options.defaults;
    Object.extend(this.options, options);
    if (this.options.showEffect && this.options.hideEffect) {
      this._effectPair = {
        show:   this.options.showEffect,
        hide:   this.options.hideEffect
      };
    }
    else if (this.options.effectPair == 'random') {
      this._usedEffectPairs    = [];
      this._unusedEffectPairs  = [];
      for (type in Slideshow.Options.effectPairs) {
        this._unusedEffectPairs.push(type);
      }
    }
    else if (Slideshow.Options.effectPairs[this.options.effectPair]) {
      this._effectPair = Slideshow.Options.effectPairs[this.options.effectPair];
    }
    else {
      throw('Slideshow requires a valid effectPair or showEffect AND hideEffect to work');
    }
    this.options.delay = parseInt(this.options.delay * 1000);
    this._isRandom = this.options.randomize;

    var showTime = parseInt((this.options.showOptions.duration || 1.0) * 1000);
    var hideTime = parseInt((this.options.hideOptions.duration || 1.0) * 1000);
    if (showTime > hideTime) {
      this.options.delay += showTime;
    }
    else {
      this.options.delay += hideTime;
    }

    this._usedFrames   = [];
    this._unusedFrames = $A(this.element.childNodes);
    this._unusedFrames.each( function (el) { Element.hide(el); } );

    this.isPaused = this.options.startPaused || false;
    this.isDone   = false;
    this._nextFrame();
  },
  _getNextFrame: function () {
    if (! this._unusedFrames.length) {
      if (this.options.repeat && this._usedFrames.length) {
        this._unusedFrames = this._usedFrames;
        this._usedFrames   = [this._unusedFrames.pop()];
      }
      else {
        this.isDone = true;
        return null;
      }
    }

    var nextFrame = null;
    var numUnused = this._unusedFrames.length;
    if (this._isRandom && numUnused > 1) {
      var index = Math.floor(Math.random()*numUnused);
      nextFrame = this._unusedFrames[index];
      this._unusedFrames.splice(index, 1);
    }
    else {
      nextFrame = this._unusedFrames.shift();
    }
    this._usedFrames.push(nextFrame);

    return nextFrame;
  },
  _nextFrame: function () {
    if (! this.isPaused) {
      this._changeFrame();
    }
  },
  _changeFrame: function () {
    var newFrame = this._getNextFrame();
    if (newFrame) {
      if (this.options.beforeChange) {
        this.options.beforeChange(this);
      }

      if (this.options.effectPair == 'random') {
        if (! this._unusedEffectPairs.length) {
          this._unusedEffectPairs  = this._usedEffectPairs;
          this._usedEffectPairs    = [ this._unusedEffectPairs.pop() ];
        }
        if (this._unusedEffectPairs.length) {
          var index = Math.floor(Math.random()*this._unusedEffectPairs.length);
          var nextPair = this._unusedEffectPairs[index];
          this._unusedEffectPairs.splice(index, 1);
          this._usedEffectPairs.push(nextPair);
          this._effectPair = Slideshow.Options.effectPairs[nextPair];
        }
        else {
          throw('Could not determine next random effectPair');
        }
      }
      var currZ = 1;
      if (this.currentFrame) {
        this.currentFrame.style.zIndex = (this.currentFrame.style && this.currentFrame.style.zIndex)
          ? this.currentFrame.style.zIndex : '1';
        currZ = parseInt(this.currentFrame.style.zIndex);
        this._effectPair['hide'](this.currentFrame, this.options.hideOptions);
      }
      newFrame.style.zIndex = '' + (++currZ);
      this._effectPair['show'](newFrame, this.options.showOptions);
      this.currentFrame = newFrame;

      if (this.options.afterChange) {
        this.options.afterChange(this);
      }

      if (! this.isPaused) {
        setTimeout(this._nextFrame.bind(this), this.options.delay);
      }
    }
  },
  pause: function () {
    this.isPaused = true;
  },
  play: function () {
    this.isPaused = false;
    this._isRandom = this.options.randomize;
    this._nextFrame();
  },
  goBack: function () {
    this._isRandom = false;
    this.pause();
    if (this._usedFrames.length > 1) {
      this._unusedFrames.unshift(this._usedFrames.pop());
      this._unusedFrames.unshift(this._usedFrames.pop());
    }
    else if (this.options.repeat) {
      this._unusedFrames.unshift(this._usedFrames.pop());
      this._unusedFrames.unshift(this._unusedFrames.pop());
    }
    this._changeFrame();
  },
  goForward: function () {
    this._isRandom = false;
    this.pause();
    this._changeFrame();
  },
  repeat: function () {
    this.options.repeat = true;
    if (this.isDone) {
      this.isDone = false;
      this._nextFrame();
    }
  },
  stopRepeating: function () {
    this.options.repeat = false;
  },
  addFrames: function(html) {
    var temp = document.createElement('DIV');
    temp.innerHTML = html;
    Element.cleanWhitespace(temp);
    var newChildren = temp.childNodes.length;
    for (var i=0; i<newChildren; i++) {
      var addedFrame = temp.childNodes[i];
      Element.hide(addedFrame);
      this.element.appendChild(addedFrame);
      this._unusedFrames.push(addedFrame);
    }
  }
} );

Ajax.Slideshow = Class.create();
Object.extend(Object.extend(Ajax.Slideshow.prototype, Effect.Slideshow.prototype), {
  initialize: function (element, url, options) {
    this.element    = $(element);
    this.url        = url;
    this._shared_initialize(options);
    this._doAjax    = true;
    if (this.options.beforeChange) {
      this._oldBeforeChange = this.options.beforeChange;
      this.options.beforeChange = function () { this._oldBeforeChange(this); this._requestFrames(); }.bind(this);
    }
    else {
      this.options.beforeChange = this._requestFrames.bind(this);
    }
  },
  _requestFrames: function () {
    if (this._doAjax && this._unusedFrames.length <= this.options.minFrames) {
      new Ajax.Request(this.url, { method: 'get', onComplete: this._addFrames.bindAsEventListener(this) });
    }
  },
  _addFrames: function (request) {
    if (request.responseText) {
      this.addFrames(request.responseText);
      if (this.options.afterRequest) {
        this.options.afterRequest(this);
      }
      if (this.isDone) {
        this.isDone = false;
        this._nextFrame();
      }
    }
    else {
      // nothing returned means no more images to load, so stop trying
      this.stopRequesting();
    }
  },
  stopRequesting: function () {
    this.options.beforeChange = this._oldBeforeChange || null;
  },
  changeUrl: function (url) {
    this.url = url;
  }
} );

Slideshow.Local = Class.create();
Object.extend(Object.extend(Slideshow.Local.prototype, Effect.Slideshow.prototype), {
  initialize: function (element, frames, options) {
    this.element    = $(element);
    this._spareFrames   = frames;
    this._shared_initialize(options);
    if (this.options.beforeChange) {
      this._oldBeforeChange = this.options.beforeChange;
      this.options.beforeChange = function () { this._oldBeforeChange(this); this._addFrames(); }.bind(this);
    }
    else {
      this.options.beforeChange = this._addFrames.bind(this);
    }
  },
  _addFrames: function () {
    if (this._unusedFrames.length <= this.options.minFrames && this._spareFrames.length) {
      var newFrame = this._spareFrames.shift();
      if (newFrame.indexOf('<') == -1) {
        var img = document.createElement('IMG');
        img.src = newFrame;
        Element.hide(img);
        this.element.appendChild(img);
        this._unusedFrames.push(img);
      }
      else {
        this.addFrames(newFrame);
      }
    }
    else {
      this.options.beforeChange = this._oldBeforeChange || null;
    }
  }
} );
