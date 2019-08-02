<?php
$con=mysqli_connect("localhost","root","","friendbuzz");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$sql="SELECT DISTINCT gallery FROM photos WHERE user='mayank'";
$result=mysqli_query($con,$sql);
$rowcount=mysqli_num_rows($result);
echo $rowcount;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo $row['gallery'];
}
mysqli_close($con);
?>