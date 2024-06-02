<?php
    $stdin = fgets(STDIN);
    $data = json_decode($stdin, true);
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?=$data['title']?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="/css/main.css" rel="stylesheet"/>
  </head>
  <body>

    <div class="container container-border">
      <header class="row">

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
          <?php else: ?>
            　
          <?php endif; ?> 
        </div>

        <div class="col text-end">
          <?php if (array_key_exists('next', $data['links'])): ?> 
            次: <a href="<?=$data['links']['next']['name']?>">
            <?=$data['links']['next']['title']?> 
            </a>
          <?php endif; ?> 
        </div>

      </header>

      <main>

        <?php if (array_key_exists('title', $data)): ?> 
          <h1><?=$data['title']?></h1>
        <?php endif; ?>

        <?php if (array_key_exists('date', $data)): ?> 
          <p><?=$data['date']?></p>
        <?php endif; ?>

        <?php if (array_key_exists('contents', $data)): ?> 
          <?=$data['contents']?>
        <?php endif; ?>
        
        <?php if (array_key_exists('files', $data)): ?>
          
          <ul>

            <?php foreach ($data['files'] as $filename): ?> 
              <li>
                <a href="<?=$filename['name']?>"><?=$filename['title']?></a>
                <?php if (array_key_exists('excerpt', $filename)): ?>
                  <p><?=$filename['excerpt']?></p>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>

          </ul>
          
        <?php endif; ?>
        
      </main>
    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  </body>
</html>
