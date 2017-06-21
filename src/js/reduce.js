function(key, values) {
    var result = {
        count: 0,
        millis: { min: Infinity, max: 0, avg: 0 },
        nreturned: { min: Infinity, max: 0, avg: 0 },
        keysExamined: { min: Infinity, max: 0, avg: 0 },
        responseLength: { min: Infinity, max: 0, avg: 0 },
        ts: { min: null, max: null }
    };

    values.forEach(function(value) {
        result.count += value.count;
        ["millis", "nreturned", "keysExamined", "responseLength"].forEach(function(field){
            result[field].min = Math.min(result[field].min, value[field].min);
            result[field].max = Math.max(result[field].max, value[field].max);
            result[field].avg += value[field].avg;
        });

        if (!result.ts.min || result.ts.min > value.ts.min) {
            result.ts.min = value.ts.min;
        }

        if (!result.ts.max || result.ts.max < value.ts.max) {
            result.ts.max = value.ts.max;
        }
    });

    return result;
};
