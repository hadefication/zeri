<?php /**
@todo:
*/?>
<div class="frame-code-container <?php echo (!$has_frames ? 'empty' : '') ?>">
  <?php foreach ($frames as $i => $frame): ?>
    <?php $line = $frame->getLine(); ?>
      <div class="frame-code <?php echo ($i == 0 ) ? 'active' : '' ?>" id="frame-code-<?php echo $i ?>">
        <div class="frame-file">
          <?php $filePath = $frame->getFile(); ?>
          <?php if ($filePath && $editorHref = $handler->getEditorHref($filePath, (int) $line)): ?>
            <a href="<?php echo $editorHref ?>" class="editor-link"<?php echo ($handler->getEditorAjax($filePath, (int) $line) ? ' data-ajax' : '') ?>>
              Open:
              <strong><?php echo $tpl->breakOnDelimiter('/', $tpl->escape($filePath ?: '<#unknown>')) ?></strong>
            </a>
          <?php else: ?>
            <strong><?php echo $tpl->breakOnDelimiter('/', $tpl->escape($filePath ?: '<#unknown>')) ?></strong>
          <?php endif ?>
        </div>
        <?php
 
if ($line !== null):


$range = $frame->getFileLines($line - 20, 40);


if ($range):
$range = array_map(function ($line) { return empty($line) ? ' ' : $line;}, $range);
$start = key($range) + 1;
$code = join("\n", $range);
?>
            <pre class="code-block line-numbers"
              data-line="<?php echo $line ?>"
              data-line-offset="<?php echo $start ?>"
              data-start="<?php echo $start ?>"
            ><code class="language-php"><?php echo $tpl->escape($code) ?></code></pre>

          <?php endif ?>
        <?php endif ?>

        <?php $frameArgs = $tpl->dumpArgs($frame); ?>
        <?php if ($frameArgs): ?>
          <div class="frame-file">
              Arguments
          </div>
          <div id="frame-code-args-<?=$i?>" class="code-block frame-args">
              <?php echo $frameArgs; ?>
          </div>
        <?php endif ?>

        <?php
 
$comments = $frame->getComments();
?>
        <div class="frame-comments <?php echo empty($comments) ? 'empty' : '' ?>">
          <?php foreach ($comments as $commentNo => $comment): ?>
            <?php extract($comment) ?>
            <div class="frame-comment" id="comment-<?php echo $i . '-' . $commentNo ?>">
              <span class="frame-comment-context"><?php echo $tpl->escape($context) ?></span>
              <?php echo $tpl->escapeButPreserveUris($comment) ?>
            </div>
          <?php endforeach ?>
        </div>

      </div>
  <?php endforeach ?>
</div>
