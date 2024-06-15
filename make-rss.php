<?php
    $stdin = fgets(STDIN);
    $sitename = $argv[1];
    $data = json_decode($stdin, true);
    $siteurl = $argv[2];
    $publisher = "Tanaka Takayuki";
    date_default_timezone_set("Asia/Tokyo");
?>
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title><?=$sitename?> RSS</title>
    <link><?=$siteurl?></link>
    <description>T's Backdoor 最新記事</description>
    <category>diary</category>
    <copyright>2024 田中 喬之</copyright>
    <language>ja_JP</language>
    <?php foreach ($data as $file): ?> 
      <item>
        <title><?=$file['title']?></title>
        <link><?=$siteurl?><?=str_replace('%2F', '/', urlencode($file['path']))?></link>
        <?php if (array_key_exists('excerpt', $file)): ?> 
          <description><?=$file['excerpt']?></description>
        <?php else: ?>
          <description></description>
        <?php endif; ?> 
        <?php if (array_key_exists('date_iso', $file)): ?> 
          <pubDate><?=date('r', strtotime($file['date_iso']))?></pubDate>
        <?php endif; ?> 
        <?php if (array_key_exists('subject', $file)): ?> 
          <category><?=$file['subject']?></category>
        <?php endif; ?> 
        <publisher><?=$publisher?></publisher>
      </item>
    <?php endforeach; ?>
  </channel>
</rss>
