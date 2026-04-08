@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{!! str_replace('Gracesoft', 'GraceSoft', $slot) !!}
</a>
</td>
</tr>