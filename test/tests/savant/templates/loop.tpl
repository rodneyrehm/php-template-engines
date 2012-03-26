<ul>
  <?php foreach ($this->values as $key => $val): ?>
    <li><?php echo $this->eprint($key); ?>: <?php echo $this->eprint($val); ?></li>
  <?php endforeach; ?>
</ul>