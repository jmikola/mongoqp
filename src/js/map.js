function() {
    if (this.query) {
        // Ignore explained queries
        if (this.query.$explain === true) {
            return;
        } else if (this.query.$explain === false && this.query.query) {
            this.query = this.query.query;
        }
    }

    // Handle old response length
    if (this.reslen) {
        this.responseLength = this.reslen;
    }

    var key = {
        op: this.op,
        ns: this.ns,
        query: this.query ? clean(this.query) : null
    };

    var value = {
        count: 1,
        millis: { min: this.millis, max: this.millis, avg: this.millis },
        nreturned: { min: this.nreturned, max: this.nreturned, avg: this.nreturned },
        nscanned: { min: this.nscanned, max: this.nscanned, avg: this.nscanned },
        responseLength: { min: this.responseLength, max: this.responseLength, avg: this.responseLength },
        ts: { min: this.ts, max: this.ts }
    };

    emit(key, value);
};
