function(key, value) {
    ["millis", "nreturned", "nscanned", "responseLength"].forEach(function(field){
        value[field].avg = value[field].avg / value.count;
    });
    return value;
};
