activate_this = '/data/environments/atlas/bin/activate_this.py'
execfile(activate_this, dict(__file__=activate_this))
import sys
path = '/data/code/atlas'
if path not in sys.path:
    sys.path.append(path)
from run import app as application
