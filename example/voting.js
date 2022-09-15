E = x =>
  x
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

S = () =>
  Math.random().toString(16).substr(2);

R = () => {
  document.querySelector('#i').innerHTML = '<iframe name=x></iframe>';
  document.querySelector('iframe').onload = L;
};

W = () => {
  s = (m + 2) % 9 + S();
  document.querySelector('#_').innerHTML =
    '<input name=f type=hidden value=' +
    h +
    '><input name=c type=hidden value=' +
    s +
    '>';
};

L = () => {
  document.querySelector('#i').innerHTML =
    "<link type=text/css rel='stylesheet noreferrer' referrerpolicy=no-referrer href=((form_action))" +
    document.querySelector('[name="s"]')
    .value
    .replace(/[+]/g, '-')
    .replace(/\//g, '_') +
    '/' +
    s +
    '.css onload=C()>';
};

C = () => {
  a =
    decodeURIComponent(
      window.getComputedStyle(document.getElementById('_'), '::after').content
        .replace(/^[^'"]*['"]/, '')
        .replace(/['"][^'"]*$/, '')
        .replace(/[+]/g, ' ')
    );
  a = a && a != 'none' ? JSON.parse(a) : [];

  t = '<button onclick=N() type=button>New poll</button><table><tr><th>Edit';
  d = [];
  for (i = 0; i < o.length; i++) {
    d[i] = 0;
    t += '<th><input readonly value="' + E(o[i]) + '">';
  }

  for (k = 0; k < a.length; k++) {
    v = a[k];
    t += '<tr><td><input type=radio name=i value=' + k + '><td>' + E(v[0]);
    d[0]++;
    j = 1;
    for (i = 1; i < o.length; i++) {
      c = 0;
      if (v[j] == i) {
        c = 'checked';
        d[i]++;
        j++;
      }
      t += '<td><input disabled type=checkbox ' + c + '>';
    }
  }

  t += '<tr><td>';
  for (i = 0; i < o.length; i++)
    t += '<td>' + d[i];

  t += '<tr><td><input type=radio name=i checked value=-1';
  for (i = 0; i < o.length; i++)
    t += '><td><input name=v[] ' +
    (i ? 'type=checkbox value=' + i : 'required');


  W();
  document.querySelector('#_').innerHTML +=
    t +
    '></table>' +
    (e - m + 8) % 9 +
    ' days left<input type=submit value=Vote><a target=_blank href="' +
    E(
      u ?
        u + '#' + w.href.substr(0, w.href.lastIndexOf('#') - 1) :
        w.href
    ) +
    '">Share link</a>';
};

N = () => {
  w.hash = u ? '##' + u : '';
  H();
};

P = () => {
  l = '?h=' + m + S() + '&e=' + m;
  o = document.querySelectorAll('input');
  for (i = 2; i < o.length; i++)
    if (q = o[i].value)
      l += '&o=' + encodeURIComponent(q);
  w.hash = l + (u ? '##' + u : '');
  H();
};

H = () => {
  w = window.location;
  m = Math.trunc(new Date().getTime() / 1e8 % 9);

  u = '';
  b = w.hash.substr(1);
  if ((i = b.lastIndexOf('#')) >= 0) {
    u = b.substr(i + 1);
    b = b.substr(0, i - 1);
  }

  p = new URLSearchParams(b);
  o = p.getAll('o');
  h = p.get('h');
  e = p.get('e');

  if (h) {
    W();
    R();
    document.forms[0].submit();
  } else
    document.querySelector('#_').innerHTML =
      '<button onclick=P() type=button>CreatePoll</button><label>Description<input></label><label>Choices</label>' +
      '<input>'.repeat(8);
};

H();
