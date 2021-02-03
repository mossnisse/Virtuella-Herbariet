class dateTimeZ {
	constructor(UTZString, timeZoneL) {
		var UTCYear = UTZString.substring(0,4);
		var UTCMonth = UTZString.substring(5,7);
		var UTCDay = UTZString.substring(8,10);
		var UTCHours = UTZString.substring(11,13);
		var UTCMinutes = UTZString.substring(14,16);
		var UTCSeconds = UTZString.substring(17,19);
		this.dateTime = new Date();
		this.dateTime.setUTCFullYear(UTCYear);
		this.dateTime.setUTCMonth(UTCMonth);
		this.dateTime.setUTCDate(UTCDay);
		this.dateTime.setUTCHours(UTCHours);
		this.dateTime.setUTCMinutes(UTCMinutes);
		this.dateTime.setUTCSeconds(UTCSeconds);
		this.dateTimeZone = new Date(this.dateTime.toLocaleString('en-US', { timeZone: timeZoneL }));
	}
	get date() {
		var zoneYear = this.dateTimeZone.getFullYear();
		var zoneMonth = this.dateTimeZone.getMonth();
		if (zoneMonth < 10) {
			zoneMonth = '0'+zoneMonth;
		}	
		var zoneDay = this.dateTimeZone.getDay();
		if (zoneDay <10) {
			zoneDay = '0'+zoneDay;
		}
		return zoneYear+'-'+zoneMonth+'-'+zoneDay;
	}
	get time() {
		var zoneHours = this.dateTimeZone.getHours();
		if (zoneHours<10) {
			zoneHours='0'+zoneHours;
		}
		var zoneMinutes = this.dateTimeZone.getMinutes();
		if (zoneMinutes<10) {
			zoneMinutes = '0'+zoneMinutes;
		}
		return zoneHours+':'+zoneMinutes;
	}
};