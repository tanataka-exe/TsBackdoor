<?php
    require 'vendor/autoload.php';
    use Michelf\MarkdownExtra;
    $sitename = $argv[1];
    $stdin = fgets(STDIN);
    $data = json_decode($stdin, true);
    $markdown = new MarkdownExtra;
    $markdown->hard_wrap = true;
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
     });
    </script>
    <link href="/css/main.css" rel="stylesheet"/>
  </head>
  <body class="bg-light">

    <div class="container bg-white container-border">
      <header class="row bg-primary">
        <div class="d-flex">
          <?php if (array_key_exists('breadcrumb', $data) && count($data['breadcrumb']) > 1): ?>

            <div class="p-2">
              <h1><strong><?=$sitename?></strong></h1>
            </div>

            <div class="p-2 flex-grow-1">
              <ul class="breadcrumb" style="margin-bottom: 0">
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

      <main>

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
          <?=$markdown->transform($data['contents'])?>
        <?php endif; ?>
        
        <?php if (array_key_exists('files', $data)): ?>
          
          <table class="table table-borderless table-hover"> 

            <?php foreach ($data['files'] as $i=>$filedata): ?> 
              <tr>
                <th scope="row text-end" width="40px"><?=$i+1?></th>
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

      </main>
      
      <footer class="row text-center">
        <p>(C)2024</p>
      </footer>

    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  </body>
</html>
