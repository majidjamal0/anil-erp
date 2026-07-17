<?php
it('reports API health', function () { $this->getJson('/api/health')->assertOk()->assertJsonPath('status', 'ok'); });
