import Plugin from 'src/plugin-system/plugin.class';
import Markdown from 'markdown-it'

const md = new Markdown();

export default class EclmPlugin extends Plugin {
    init() {


        console.log(md.render('# markdown works'));
        window.onscroll = function() {
            if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight) {
                alert('Hello there!!!');
            }
        };
    }
}
