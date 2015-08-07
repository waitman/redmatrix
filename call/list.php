<?php
$conn=mysqli_connect('127.0.0.1','asterisk','groupie44','asterisk');
$sql = "SELECT * FROM bit_cdr ORDER BY calldate DESC";
$res = mysqli_query($conn,$sql);
$content = '<table border="1" cellspacing="0" cellpadding="3" width="100%" align="center">
<tr style="background-color:#00BFFF;">
<td><strong>Call Date</strong></td>
<td><strong>Caller Id</strong></td>
<td><strong>Source</strong></td>
<td><strong>Destination</strong></td>
<td><strong>Context</strong></td>
<td><strong>Channel</strong></td>
<td><strong>DST Channel</strong></td>
<td><strong>Last App</strong></td>
<td><strong>Last Data</strong></td>
<td><strong>Duration</strong></td>
<td><strong>Bill Sec</strong></td>
<td><strong>Disposition</strong></td>
<td><strong>AMA Flags</strong></td>
<td><strong>Account Code</strong></td>
<td><strong>User Field</strong></td>
<td><strong>Unique Id</strong></td>
<td><strong>Linked Id</strong></td>
<td><strong>Sequence</strong></td>
<td><strong>Peer Account</strong></td>
</tr>
';

while ($row = mysqli_fetch_array($res))
{
	$content .= '<tr>
<td>'.$row['calldate'].'</td>
<td>'.$row['clid'].'</td>
<td>'.$row['src'].'</td>
<td>'.$row['dst'].'</td>
<td>'.$row['dcontext'].'</td>
<td>'.$row['channel'].'</td>
<td>'.$row['dstchannel'].'</td>
<td>'.$row['lastapp'].'</td>
<td>'.$row['lastdata'].'</td>
<td>'.$row['duration'].'</td>
<td>'.$row['billsec'].'</td>
<td>'.$row['disposition'].'</td>
<td>'.$row['amaflags'].'</td>
<td>'.$row['accountcode'].'</td>
<td>'.$row['userfield'].'</td>
<td>'.$row['uniqueid'].'</td>
<td>'.$row['linkedid'].'</td>
<td>'.$row['sequence'].'</td>
<td>'.$row['peeraccount'].'</td>
</tr>
';
}

$content .= '
</table>
';
mysqli_free_result($res);
mysqli_close($conn);

$layout = file_get_contents('layout.html');
echo str_replace('<!--Content-->',$content,$layout);

