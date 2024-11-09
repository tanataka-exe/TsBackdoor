<?php
    //    TsBackdoor - a poor static site generator for my own use.
    //    Copyright (C) 2024  Tanaka Takayuki
    //
    //    This program is free software: you can redistribute it and/or modify
    //    it under the terms of the GNU General Public License as published by
    //    the Free Software Foundation, either version 3 of the License, or
    //    (at your option) any later version.
    //
    //    This program is distributed in the hope that it will be useful,
    //    but WITHOUT ANY WARRANTY; without even the implied warranty of
    //    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    //    GNU General Public License for more details.
    //
    //    You should have received a copy of the GNU General Public License
    //    along with this program.  If not, see <https://www.gnu.org/licenses/>.

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
        <guid><?=str_replace('%2F', '/', urlencode($file['path']))?></guid>
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
