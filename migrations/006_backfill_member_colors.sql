UPDATE plan_members pm
JOIN (
    SELECT id, ROW_NUMBER() OVER (PARTITION BY plan_id ORDER BY id) - 1 AS n
    FROM plan_members
   ) t ON t.id = pm.id
SET pm.color = ELT(t.n % 8 + 1, '#89CFF0', '#F4A261', '#90E0A0', '#F28BB4', '#C3A6FF', '#F6D860', '#6EDCD0' '#FF8A80')