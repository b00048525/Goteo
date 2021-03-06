<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */


use Goteo\Library\Text;

$invest = $this['invest'];
$project = $this['project'];
$campaign = $this['campaign'];
$user = $this['user'];

$rewards = $invest->rewards;
array_walk($rewards, function (&$reward) { $reward = $reward->reward; });

?>
<div class="widget">
    <p>
        <strong>Proyecto:</strong> <?php echo $project->name ?> (<?php echo $this['status'][$project->status] ?>)
        <strong>Usuario: </strong><?php echo $user->name ?> [<?php echo $user->email ?>]
    </p>
    <p>
        <?php if ($project->status == 3 && ($invest->status < 1 || ($invest->method == 'tpv' && $invest->status < 2) ||($invest->method == 'cash' && $invest->status < 2))) : ?>
        <a href="/admin/invests/cancel/<?php echo $invest->id ?>"
            onclick="return confirm(Text::_("¿Estás seguro de querer cancelar este aporte y su preapproval?"));"
            class="button red">Cancelar este aporte</a>&nbsp;&nbsp;&nbsp;
        <?php endif; ?>

        <?php if ($project->status == 3 && $invest->method == 'paypal' && $invest->status == 0) : ?>
        <a href="/admin/invests/execute/<?php echo $invest->id ?>"
            onclick="return confirm(Text::_("¿Seguro que quieres ejecutar ahora? ¿No quieres esperar a la ejecución automática al final de la ronda? ?"));"
            class="button red"><?php echo Text::_("Ejecutar cargo ahora"); ?></a>
        <?php endif; ?>

        <?php if ($project->status == 3 && $invest->method != 'paypal' && $invest->status == 1) : ?>
        <a href="/admin/invests/move/<?php echo $invest->id ?>" class="button weak"><?php echo Text::_("Reubicar este aporte"); ?></a>
        <?php endif; ?>
    </p>

    <h3><?php echo Text::_("Detalles del aporte"); ?></h3>
    <dl>
        <dt><?php echo Text::_("Cantidad aportada:"); ?></dt>
        <dd><?php echo $invest->amount ?> &euro;
            <?php
                if (!empty($invest->campaign))
                    echo Text::_("Campaña") .': '. $campaign->name;
            ?>
        </dd>
    </dl>
    
    <dl>
        <dt>Estado:</dt>
        <dd><?php echo $this['investStatus'][$invest->status]; if ($invest->status < 0) echo ' <span style="font-weight:bold; color:red;"><?php echo Text::_("OJO! que este aporte no fue confirmado."); ?><span>';  ?></dd>
    </dl>

    <dl>
        <dt>Fecha del aporte:</dt>
        <dd><?php echo $invest->invested . '  '; ?>
            <?php
                if (!empty($invest->charged))
                    echo  Text::_("Cargo ejecutado el: ") . $invest->charged;

                if (!empty($invest->returned))
                    echo  Text::_("Dinero devuelto el: ") . $invest->returned;
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Método de pago:</dt>
        <dd><?php echo $invest->method . '   '; ?>
            <?php
                if (!empty($invest->anonymous))
                    echo '<br />'.Text::_("Aporte anónimo");

                if (!empty($invest->resign))
                    echo "<br />" .Text::_("Donativo de") . " :{$invest->address->name} [{$invest->address->nif}]";

                if (!empty($invest->admin))
                    echo '<br />'.Text::_("Manual generado por admin: ").$invest->admin;
            ?>
        </dd>
    </dl>

    <dl>
        <dt><?php echo Text::_("Códigos de seguimiento: "); ?><a href="/admin/accounts/details/<?php echo $invest->id ?>"><?php echo Text::_("Ir a la transacción"); ?></a></dt>
        <dd><?php
                if (!empty($invest->preapproval))
                    echo 'Preapproval: '.$invest->preapproval . '   ';
                
                if (!empty($invest->payment)) 
                    echo 'Cargo: '.$invest->payment . '   ';
            ?>
        </dd>
    </dl>

    <?php if (!empty($invest->rewards)) : ?>
    <dl>
        <dt><?php echo Text::_("Recompensas elegidas:"); ?></dt>
        <dd>
            <?php echo implode(', ', $rewards); ?>
        </dd>
    </dl>
    <?php endif; ?>

    <dl>
        <dt><?php echo Text::_("Dirección:"); ?></dt>
        <dd>
            <?php echo $invest->address->address; ?>,
            <?php echo $invest->address->location; ?>,
            <?php echo $invest->address->zipcode; ?>
            <?php echo $invest->address->country; ?>
        </dd>
    </dl>
</div>
