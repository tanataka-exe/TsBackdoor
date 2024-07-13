async function readRss() {
    const rssResponse = await fetch('/rss.xml');
    const parser = new DOMParser();
    const rssXmlText = await rssResponse.text();
    const rss = parser.parseFromString(rssXmlText, 'text/xml');
    const items = rss.querySelectorAll('item');
    for (let i = 0; i < items.length && i < 20; i++) {
        const item = items[i];
        const title = item.getElementsByTagName('title')[0].innerHTML;
        const link = item.getElementsByTagName('link')[0].innerHTML;
        const pubDate = item.getElementsByTagName('pubDate')[0].innerHTML;
        const date = new Date(pubDate);
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate();
        const dateFormat = `${year}/${month}/${day}`
        $('#recent-articles').find('tbody').append(`
            <tr>
              <th scope="row" class="text-end" width="40px">${i + 1}</th>
              <td><a href="${link}">${title}</td>
              <td><span class="date">${dateFormat}</span></td>
            </tr>
        `);
    }
}

$(document).ready(() => {
    readRss();
});
