<?
mail("manifesto@manifesto.blog.br","Contact from IdeasWall.org","From: ".$_POST["name"]." <".$_POST["email"].">\n\n".$_POST["content"]);
echo "<script>alert('Thank you for your message.'); location.href='static/index.html'</script>";
?>
