<?php
    require 'vendor/autoload.php';
    use Michelf\MarkdownExtra;
    $sitename = $argv[1];
    $stdin = fgets(STDIN);
    $data = json_decode($stdin, true);
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

      <script>
       $(document).ready(() => {
           hljs.highlightAll();
       });
      </script>
    <?php endif; ?> 

    <link href="/css/main.css" rel="stylesheet"/>
  </head>
  <body class="bg-light">

    <div class="container bg-white container-border">
      <header class="row bg-primary">
        <?php if (array_key_exists('breadcrumb', $data) && count($data['breadcrumb']) > 0): ?>
          <div class="d-flex flex-row">

            <div class="p-2">
              <span><strong><?=$sitename?></strong></span>
            </div>

            <div class="p-2">
              <ul class="breadcrumb" style="margin-bottom: 0">
                <?php foreach ($data['breadcrumb'] as $item): ?>
                  <li class="breadcrumb-item"><a href="<?=$item['name']?>"><?=$item['title']?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
            
          </div>

        <?php else: ?>
          <div class="d-flex flex-row">
            <div class="p-2">
              <span class="text-center"><strong><?=$sitename?></strong></span>
            </div>
          </div>
        <?php endif; ?>

      </header>

      <main>

        <?php if (array_key_exists('breadcrumb', $data) && count($data['breadcrumb']) > 0): ?>
          <div class="row">
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
          </div>
        <?php endif; ?>

        <?php if (array_key_exists('title', $data)): ?> 
          <h1><?=$data['title']?></h1>
        <?php endif; ?>

        <?php if (array_key_exists('date', $data)): ?> 
          <p><?=$data['date']?></p>
        <?php endif; ?>


        <?php if (array_key_exists('contents', $data)): ?> 
          <?=MarkdownExtra::defaultTransform($data['contents'])?>
        <?php endif; ?>
        
        <?php if (array_key_exists('files', $data)): ?>
          
          <ol>

            <?php foreach ($data['files'] as $filename): ?> 
              <li>
                <a href="<?=$filename['name']?>"><?=$filename['title']?></a>
                <?php if (array_key_exists('excerpt', $filename)): ?>
                  <p><?=$filename['excerpt']?></p>
                <?php endif; ?> 
              </li>
            <?php endforeach; ?>

          </ol>
          
        <?php endif; ?>

        <?php if ($data['number_of_lines'] > 100): ?>
          <div class="row">
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
          </div>
        <?php endif; ?>

      </main>
      
      <footer class="row text-center">
        <p>(C)2024</p>
      </footer>

    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  </body>
</html>
