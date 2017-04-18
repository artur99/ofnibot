function weAreOn(what){
    what = what.toLowerCase();
    if(what == 'chatapp') return currenturl == 'GET_app';

    return false;
}
