<?php
    $stdin = fgets(STDIN);
    $sitename = $argv[1];
    $data = json_decode($stdin, true);
    $siteurl = $argv[2];
    $publisher = "Tanaka Takayuki";
?>
<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF xmlns="http://purl.org/rss/1.0/"  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/"  xml:lang="ja">
  <channel rdf:about="<?=$siteurl?>/news.rdf">
    <title><?=$sitename?> RSS</title>
    <link><?=$siteurl?></link>
    <description>T's Backdoor 最新記事</description>
    <items>
      <?php foreach ($data as $file): ?> 
        <item rdf:about="<?=$siteurl?><?=$file['path']?>">
          <title><?=$file['title']?></title>
          <link><?=$siteurl?><?=urlencode($file['path'])?></link>
          <?php if (array_key_exists('excerpt', $file)): ?> 
            <description><?=$file['excerpt']?></description>
          <?php endif; ?> 
          <?php if (array_key_exists('date_iso', $file)): ?> 
            <dc:date><?=$file['date_iso']?></dc:date>
          <?php endif; ?> 
          <?php if (array_key_exists('subject', $file)): ?> 
            <dc:subject><?=$file['subject']?></dc:subject>
          <?php endif; ?> 
          <dc:publisher><?=$publisher?></dc:publisher>
        </item>
      <?php endforeach; ?>
    </items>
  </channel>
</rdf:RDF>
