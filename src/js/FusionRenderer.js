/*
* Copyright (c) 2016 Savino Systems
*/
var FusionRenderer = (function(){
    var _instance = null;

    var _pixelRatio = window.devicePixelRatio || 1;
    var _consts = {
        TRI_INTERVAL:0.1, //Seconds
        STROKE_WIDTH:4,
        PARTICLE_FACTOR:2.15,

		DRAW_STROKE:true,
		FOREGROUND_ALPHA:0x52 / 0xFF,
		PARTICLE_SPEED:10 / _pixelRatio,
		PARTICLE_SPEED_RANGE:121 / _pixelRatio,
		TRI_MAX:325,
		TRI_DIST:265 / _pixelRatio,

		COLORS:[0x00FF00, 0x00FFFF, 0x0000FF, 0xFF00FF, 0xFF0000, 0xFFFF00],
		COLOR_TIME:4	//Seconds
    };

    var _vars = {
        _gfx:null,
        _width:0,
        _height:0,

        _particleNum:0,
        _particles:null,

        _tris:null,
        _triIndex:0,
        _triTime:0,

        _colorIndex:0,
        _colorTime:0,
        _colorDiff:new Array(3) //RGB
    };

    var _methods = {
        init:function(){
            try {
                _vars._gfx = new GFXRenderer({
                    canvas:document.getElementById("fusionCanvas"),
                    onRender:_methods._render,
                    onResize:_methods._resize
                });
            } catch (ex){
                alert(ex);
                return;
            }

            _vars._tris = new Array(_consts.TRI_MAX);

            _methods._initColor();
            _methods._resize();
        },

        destroy:function(){
            var gfx = _vars._gfx;
            if (gfx){
                gfx.context.clearRect(0, 0, _vars._width, _vars._height);
                gfx.paused = false;
                gfx.destroy();
                _vars._gfx = null;
            }

            _vars._particleNum = 0;
            _vars._particles = null;
            _vars._tris = null;
        },

        pause:function(){
            var gfx = _vars._gfx;
            if (gfx){
                gfx.paused = true;
            }
        },

        resume:function(){
            var gfx = _vars._gfx;
            if (gfx){
                gfx.paused = false;
            }
        },

        isPaused:function(){
            var gfx = _vars._gfx;
            if (gfx){
                return gfx.paused;
            }
            return false;
        },

        _initParticles:function(){
			var particleNum = parseInt(Math.sqrt(_vars._width * _vars._height) / _consts.TRI_DIST * (Math.sqrt(_vars._width * _vars._height) * _consts.PARTICLE_FACTOR / _consts.TRI_DIST));
			var particles = [particleNum];

			var indicesTemp = [];
			for (var i = 0; i < particleNum; i++){
				indicesTemp.push(i);
			}
			var indices = [];
			for (var i = 0; i < particleNum; i++){
				var index = parseInt(Math.random() * indicesTemp.length);
				indices.push(indicesTemp[index]);
				indicesTemp.splice(index, 1);
			}

			var twoPi = Math.PI * 2;
			var cols = Math.floor(Math.sqrt(particleNum) * (_vars._width / _vars._height));
			var rows = Math.floor(Math.sqrt(particleNum) * (_vars._height / _vars._width));
			var numInGrid = cols * rows;
			var colSize = _vars._width / cols;
			var rowSize = _vars._height / rows;
			for (var i = 0; i < particleNum; i++){
				var col = i % cols;
				var row = Math.floor(i / cols);
				var particle = [4];
				var pSpeed = Math.random() * _consts.PARTICLE_SPEED_RANGE + _consts.PARTICLE_SPEED;
				var pAngle = Math.random() * twoPi;
				if (i < numInGrid){
					particle[0] = colSize * col + Math.random() * colSize;
					particle[1] = rowSize * row + Math.random() * rowSize;
				} else {
					particle[0] = Math.random() * _vars._width;
					particle[1] = Math.random() * _vars._height;
				}
				particle[2] = Math.cos(pAngle) * pSpeed;
				particle[3] = Math.sin(pAngle) * pSpeed;
				particle[4] = particle[2];
				particle[5] = particle[3];
				particles[i] = particle;
			}

			_vars._particleNum = particleNum;
			_vars._particles = particles;
			_vars._triIndex = 0;
        },

        _render:function(delta){
            var context = _vars._gfx.context;
            var particles = _vars._particles;
            var DRAW_STROKE = _consts.DRAW_STROKE;

            //Update particles
            var width = _vars._width;
            var height = _vars._height;
            var particleNum = _vars._particleNum;
			for (var i = 0; i < particleNum; i++){
				var particle = particles[i];
				particle[0] += particle[2] * delta;
				if (particle[0] > width){
					particle[0] = width;
					particle[2] *= -1;
					particle[4] *= -1;
				} else if (particle[0] < 0){
					particle[0] = 0;
					particle[2] *= -1;
					particle[4] *= -1;
				}
				particle[1] += particle[3] * delta;
				if (particle[1] > height){
					particle[1] = height;
					particle[3] *= -1;
					particle[5] *= -1;
				} else if (particle[1] < 0){
					particle[1] = 0;
					particle[3] *= -1;
					particle[5] *= -1;
				}
			}

            //Update tris
            var triTime = _vars._triTime;
            triTime += delta;
            if (triTime > _consts.TRI_INTERVAL){
                _methods._updateTris();

				//Color
				var colorTime = _vars._colorTime;
				colorTime += triTime;
				if (colorTime > _consts.COLOR_TIME){ //Cycle
					colorTime = 0;
					_vars._colorIndex = (_vars._colorIndex + 1) % _consts.COLORS.length;
					_methods._initColor();
				} else { //Set
					var colorTimePercent = colorTime / _consts.COLOR_TIME;
					var color = _consts.COLORS[_vars._colorIndex];
					var colorDiff = _vars._colorDiff;
					color += (colorDiff[0] * colorTimePercent) << 16;
					color += (colorDiff[1] * colorTimePercent) << 8;
					color += (colorDiff[2] * colorTimePercent) << 0;
					var hexColor = "#" + ("000000" + color.toString(16)).slice(-6);
					if (DRAW_STROKE){
						context.strokeStyle = hexColor;
					} else {
						context.fillStyle = hexColor;
					}
				}
				_vars._colorTime = colorTime;

                triTime = 0;
            }
            _vars._triTime = triTime;

            //Clear
            context.clearRect(0, 0, width, height);

            //Render tris
            var tris = _vars._tris;
            var triIndex = _vars._triIndex;
            for (var i = 0; i < triIndex; i++){
                var tri = tris[i];
                var p1 = particles[tri[0]];
                var p2 = particles[tri[1]];
                var p3 = particles[tri[2]];

                context.globalAlpha = tri[3];
                context.beginPath();
                context.moveTo(p1[0], p1[1]);
                context.lineTo(p2[0], p2[1]);
                context.lineTo(p3[0], p3[1]);
                context.closePath();

                if (DRAW_STROKE){
					context.stroke();
                } else {
					context.fill();
                }
            }
        },

        _initColor:function(){
			var colors = _consts.COLORS;
			var currentIndex = _vars._colorIndex;
			var currentColor = colors[currentIndex];
			var nextColor = colors[(currentIndex + 1) % colors.length];
			var colorDiff = _vars._colorDiff;
			colorDiff[0] = (nextColor & 0xFF0000) - (currentColor & 0xFF0000) >> 16;
			colorDiff[1] = (nextColor & 0x00FF00) - (currentColor & 0x00FF00) >> 8;
			colorDiff[2] = (nextColor & 0x0000FF) - (currentColor & 0x0000FF) >> 0;
        },

        _updateTris:function(){
            var triIndex = 0;
            var triMax = _consts.TRI_MAX;
            var foregroundAlpha =  _consts.FOREGROUND_ALPHA;
            var triDist = _consts.TRI_DIST;
            var tris = _vars._tris;
            var distX = 0;
            var distY = 0;
            var particles = _vars._particles;
            var particleNum = _vars._particleNum;
            for (var i = 0; i < particleNum; i++){
                var p1 = particles[i];
                for (var j = i + 1; j < particleNum; j++){
                    var p2 = particles[j];
                    var distX = p1[0] - p2[0];
                    var distY = p1[1] - p2[1];
                    var dist1 = Math.sqrt(distX * distX + distY * distY);
                    if (dist1 <= triDist){
                        for (var k = j + 1; k < particleNum; k++){
                            var p3 = particles[k];
                            var distX = p1[0] - p3[0];
							var distY = p1[1] - p3[1];
							var dist2 = Math.sqrt(distX * distX + distY * distY);
                            if (dist2 <= triDist){
								var distX = p2[0] - p3[0];
								var distY = p2[1] - p3[1];
								var dist3 = Math.sqrt(distX * distX + distY * distY);
                                if (dist3 <= triDist){
									var inverseDistScale = 1 - (((dist1 + dist2 + dist3) * 0.333) / triDist);

									/*var tmp = inverseDistScale * 2;
									p1[2] = p1[4] * tmp;
									p1[3] = p1[5] * tmp;
									p2[2] = p2[4] * tmp;
									p2[3] = p2[5] * tmp;
									p3[2] = p3[4] * tmp;
									p3[3] = p3[5] * tmp;*/

									var centerX = (p1[0] + p2[0] + p3[0]) * 0.333;
									var centerY = (p1[1] + p2[1] + p3[1]) * 0.333;

                                    tris[triIndex] = [i, j, k,
										inverseDistScale * foregroundAlpha
									];
									triIndex++;
                                    if (triIndex == triMax){
										_vars._triIndex = triIndex;
                                        return;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            _vars._triIndex = triIndex;
        },

        _resize:function(){
            var canvas = _vars._gfx.canvas;
            var width = canvas.width;
            var height = canvas.height;

            var particleNum = _vars._particleNum;
            for (var i = 0; i < particleNum; i++){
                var particle = _vars._particles[i];
                particle[0] = width * particle[0] / _vars._width;
				particle[1] = height * particle[1] / _vars._height;
            }
            _vars._width = width;
            _vars._height = height;

            _methods._initParticles();
        },
    };

    _instance = {
        init:_methods.init,
        destroy:_methods.destroy,
        pause:_methods.pause,
        resume:_methods.resume,
        isPaused:_methods.isPaused
    };
    _instance.init();
    return _instance;
})();