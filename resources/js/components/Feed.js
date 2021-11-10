import React, {Component} from "react";
import {Link} from 'react-router-dom';

class Feed extends Component {

    state = {
        feeds: [],
        loading: true
    }

    async componentDidMount() {

        const resp = await axios.get('/api/feeds');

        console.log(resp.data.feeds.data)

        if (resp.data.status === 200) {

            this.setState({

                feeds: resp.data.feeds.data,
                loading: false
            })
        }
    }

    render() {

        let feed_HTML_TABLE;

        if (this.state.loading) {
            feed_HTML_TABLE = <tr><td colSpan="8">Loading ...</td></tr>
        } else {

            feed_HTML_TABLE = this.state.feeds.map((item) => {
                return (
                    <tr key={item.id}>
                        <td>
                            {item.title}
                        </td>
                        <td>
                            {item.link}
                        </td>
                        <td>
                            {item.source}
                        </td>
                        <td>
                            {item.source_url}
                        </td>
                        <td>
                            {item.publish_date}
                        </td>
                        <td>
                            {item.description}
                        </td>
                        <td>
                            <Link to={`edit-feed/${item.id}`} className="btn btn-success btn-sm">Edit</Link>
                            &nbsp;
                            <Link to={`delete-feed/${item.id}`} className="btn btn-danger btn-sm">Delete</Link>
                        </td>
                    </tr>
                );
            });
        }

        return(
            <div className="container">
                <div className="row">
                    <div className="col-md-12">
                        <div className="card">
                            <div className="card-header">
                                <h4 className="text-center">Feed Preview
                                    <Link to={'add-feeds'} className="btn btn-primary btn-sm float-right"> Add Feed</Link>
                                </h4>
                            </div>
                            <div className="card-body">
                                <table className="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Link</th>
                                            <th>Source</th>
                                            <th>Source URL</th>
                                            <th>Publish Date</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {feed_HTML_TABLE}
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Feed;


