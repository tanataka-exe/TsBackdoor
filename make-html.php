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

require 'vendor/autoload.php';
use Michelf\MarkdownExtra;
$sitename = $argv[1];
$stdin = fgets(STDIN);
$data = json_decode($stdin, true);
//fputs(STDERR, $stdin);
$markdown = new MarkdownExtra;
$markdown->hard_wrap = true;
$isTop = count($data['links']) == 0;
$isIndex = $data["name"] == "index.html";
$isSidebar = isset($data["side_files"]);
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?=$data['title']?> - <?=$sitename?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/highlight/stackoverflow-light.min.css"/>
    <link rel="stylesheet" type="text/css" href="/css/highlight-ex.css"/>
    <script src="/js/highlight.min.js"></script>
    <script src="/js/jquery-3.7.1.min.js"></script>
    <?php if ($isTop): ?>
      <script src="/js/recent-articles.js"></script>
    <?php endif; ?>

    <?php if (array_key_exists('languages', $data)): ?> 
      <?php foreach ($data['languages'] as $lang): ?> 
        <script src="/js/highlight/<?=$lang?>.min.js"></script>
      <?php endforeach; ?>
    <?php endif; ?> 

    <script>
     $(document).ready(() => {
       hljs.highlightAll();

       if ($("main").height() < $(window).height()) {
         document.querySelector("#bottom-nav").style.display = "none";
       }
       $(".contents table").addClass("table table-bordered");
     });

    </script>
    <link href="/css/main.css" rel="stylesheet"/>
  </head>
  <body class="bg-light">

    <div class="container bg-white container-border">
      <header class="row">
        <div class="d-flex">
          <?php if (array_key_exists('breadcrumb', $data) && count($data['breadcrumb']) > 1): ?>

            <div class="p-2">
              <h1><strong><?=$sitename?></strong></h1>
            </div>

            <div class="p-2 flex-grow-1" style="--bs-breadcrumb-divider: '>';">
              <ul class="breadcrumb" style="margin-bottom: 0; --bs-breadcrumb-divider-color: #CCC;">
                <?php for ($i = 0; $i < count($data['breadcrumb']) - 1; $i++): ?>
                  <li class="breadcrumb-item">
                    <a href="<?=$data['breadcrumb'][$i]['name']?>"> <?=$data['breadcrumb'][$i]['title']?> </a>
                  </li>
                <?php endfor; ?>
              </ul>
            </div>
            
          <?php else: ?>
            <div class="p-2 flex-grow-1">
              <h1><strong><?=$sitename?></strong></h1>
            </div>
          <?php endif; ?>

          <div class="p-2">
            <a href="/rss.xml">RSS</a>
          </div>
          <div class="p-2">
            <a href="https://x.com/ahalogist_t">&#x1D54F;</a>
          </div>
        </div>
      </header>

      <main class="<?php if ($isSidebar) echo "d-flex flex-row"; else echo "";?>">

        <?php if ($isSidebar): ?> 
          <div id="sidebar" class="p-2">
	    <h3><a href="<?=$data["name"]=="index.html"?"../":"./"?>">↑</a> <?=$data['links']['up']['title']?></h3>
            <ul> 
              <?php foreach ($data["side_files"] as $navFile): ?>
                <?php if ($navFile["current"]): ?>
                  <?php if (isset($data["files"]) && count($data["files"]) > 0): ?>
                    <li><strong><?=$navFile["title"]?></strong>
                      <ul>
                        <?php foreach ($data["files"] as $child): ?>
                          <li><a href="<?=$child["name"]?>"><?=$child["title"]?></a></li>
                        <?php endforeach; ?>
                      </ul>
                    </li>
                  <?php else: ?>
                    <!-- current = <?=$navFile["current"] ? "true" : "false"?> -->
                    <li><strong><?=$navFile["title"]?></strong></li>
                  <?php endif; ?>
                <?php else: ?> 
                  <!-- current = <?=$navFile["current"] ? "true" : "false"?> -->
                  <li><a href="<?=$navFile["name"]?>"><?=$navFile["title"]?></a></li>
                <?php endif; ?>
              <?php endforeach; ?> 
            </ul>
          </div>
        <?php endif; ?> 

        <article class="<?php if ($isSidebar) echo "p-2"; else echo "";?>">
          <?php if (array_key_exists('breadcrumb', $data) && count($data['breadcrumb']) > 0): ?>
            <nav id="top-nav" class="row">
              <div class="col text-start">
                <?php if (array_key_exists('prev', $data['links'])): ?> 
                  前: <a href="<?=$data['links']['prev']['name']?>">
                  <?=$data['links']['prev']['title']?> 
                  </a>
                <?php endif; ?> 
              </div>

              <div class="col text-center">
                <?php if (array_key_exists('up', $data['links'])): ?> 
                  上: <a href="<?=$data['links']['up']['name']?>">
                  <?=$data['links']['up']['title']?> 
                  </a>
                <?php endif; ?> 
              </div>
              
              <div class="col text-end">
                <?php if (array_key_exists('next', $data['links'])): ?> 
                  次: <a href="<?=$data['links']['next']['name']?>">
                  <?=$data['links']['next']['title']?> 
                  </a>
                <?php endif; ?> 

              </div>
              <hr/>
            </nav>
          <?php endif; ?>

          <?php if (array_key_exists('eyecatch', $data)): ?>
            <img src="<?=$data['eyecatch']?>" style="margin-bottom: 1em;" />
          <?php endif; ?>

          <div class="article-header"> 
            <?php if (array_key_exists('title', $data)): ?> 
              <h1><?=$data['title']?></h1>
            <?php endif; ?> 

            <?php if (array_key_exists('date', $data)): ?> 
              <p class="date"><?=$data['date']?></p>
            <?php endif; ?> 
          </div>

          <?php if (array_key_exists('contents', $data)): ?> 
            <div class="contents">
              <?=$markdown->transform($data['contents'])?> 
            </div>
          <?php endif; ?>
          
          <?php if ($isIndex && array_key_exists('files', $data)): ?>

            <?php if ($isTop): ?>
              <h2>分類</h2>

              <ul class="d-flex flex-row flex-wrap btn-group">
                <?php foreach ($data['files'] as $i=>$filedata): ?> 
                  <li class="d-flex btn btn-outline-info btn-lg justify-content-around">
                    <a href="<?=$filedata['name']?>"><?=$filedata['title']?></a>
                  </li>
                <?php endforeach; ?>
              </ul>

            <?php else: ?>
              
              <table class="table table-borderless table-hover"> 

                <?php foreach ($data['files'] as $i=>$filedata): ?> 
                  <tr>
                    <th scope="row" class="text-end" width="40px"><?=$i+1?></th>
                    <?php if (isset($filedata['thumb'])): ?> 
                      <td style="width:70px;">
                        <?php if (str_ends_with($filedata['name'], ".html")): ?> 
                          <img src="<?=$filedata['thumb']?>"/>
                        <?php else: ?> 
                          <img src="<?=$filedata['name']?>/<?=$filedata['thumb']?>"/>
                        <?php endif; ?> 
                      </td>
                    <?php else: ?>
                      <td style="width:0;">
                      </td>
                    <?php endif; ?>
                    <td>
                      <a href="<?=$filedata['name']?>"><?=$filedata['title']?></a>
                    </td>
                    <td>
                      <?php if (array_key_exists('excerpt', $filedata)): ?>
                        <p><?=$filedata['excerpt']?></p>
                      <?php endif; ?> 
                    </td>
                    <?php if (array_key_exists('display_dates', $data)
                              && strtolower($data['display_dates']) != 'no'
                              && array_key_exists('date', $filedata)): ?>
                      <td class="text-end">
                        <span class="date"><?=$filedata['date']?></span>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>

              </table>

            <?php endif; ?>
          <?php endif; ?>

          <?php if ($isTop): ?>
            <h2>最近書いたページ</h2>
            <table id="recent-articles" class="table table-borderless table-hover">
              <tbody></tbody>
            </table>
          <?php endif; ?>

          <nav id="bottom-nav" class="row">
            <div class="col text-start">
              <?php if (array_key_exists('prev', $data['links'])): ?> 
                前: <a href="<?=$data['links']['prev']['name']?>">
                <?=$data['links']['prev']['title']?> 
                </a>
              <?php endif; ?> 
            </div>

            <div class="col text-center">
              <?php if (array_key_exists('up', $data['links'])): ?> 
                上: <a href="<?=$data['links']['up']['name']?>">
                <?=$data['links']['up']['title']?> 
                </a>
              <?php endif; ?> 
            </div>

            <div class="col text-end">
              <?php if (array_key_exists('next', $data['links'])): ?> 
                次: <a href="<?=$data['links']['next']['name']?>">
                <?=$data['links']['next']['title']?> 
                </a>
              <?php endif; ?> 
            </div>
          </nav>

        </article>
      </main>
      
      <footer class="row text-center">
        <p>&copy;2024 T.T.</p>
      </footer>

    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  </body>
</html>
