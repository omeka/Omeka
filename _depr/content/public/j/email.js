var spans = document.getElementsByTagName('span');
var emails = getElementsByClass('mailto');
  for( var i=0; i<emails.length; i++ ){
    if( emails[i].firstChild &&
        emails[i].firstChild.nodeValue.match( /\s+?\[at]\s+?/g ) ){
      var str = emails[i].firstChild.nodeValue;
          str = str.replace( /\s+?\[(?:dot|period)]\s+?/g, '.' );  // replaces all .
          str = str.replace( /\s+?\[(?:at)]\s+?/g, '@' );          // replaces the @
          str = str.replace( /\s+?\[(?:dash|hyphen)]\s+?/g, '-' ); // replaces all -
      var a = document.createElement( 'a' );
          a.setAttribute( 'href', 'mailto:'+str );
          a.appendChild( document.createTextNode( str ) );
      emails[i].parentNode.replaceChild( a, emails[i] );
    }
  }