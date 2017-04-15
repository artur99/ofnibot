function getUnicodeChar(emoticon){
    var data = [];
    data[':)'] = '\u{1F60A}';
    data[':D'] = '\u{1F601}';
    data[':P'] = '\u{1F60B}';
    data[';)'] = '\u{1F609}';
    return typeof data[emoticon] == 'undefined' ? '' : data[emoticon];
}

function weAreOn(what){
    what = what.toLowerCase();
    if(what == 'chatapp') return currenturl == 'GET_app';

    return false;
}
