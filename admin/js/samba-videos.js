window.SV = (function(){

	var SV = {

		CONFIG: CONFIG,

		getShortCode: function(mediaId, userParams){
			var shortCode = '[samba-player mediaId="'+mediaId+'" ';
			for (var key in userParams) {
				shortCode += key + '="' + userParams[key] + '" '; 
			}
			shortCode += ']';
			return shortCode;
		}

	};

	return SV;

})();