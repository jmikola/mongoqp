function(key, value) {
    ["millis", "nreturned", "keysExamined", "responseLength"].forEach(function(field){
        value[field].avg = value[field].avg / value.count;
    });
    return value;
};
